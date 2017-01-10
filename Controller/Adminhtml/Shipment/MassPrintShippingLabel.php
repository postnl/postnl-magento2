<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

use TIG\PostNL\Model\ResourceModel\Shipment\CollectionFactory as PostnlShipmentCollectionFactory;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class MassPrintShippingLabel extends Action
{
    /**
     * @var Shipment
     */
    private $currentShipment;

    /**
     * @var array
     */
    private $labels;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PostnlShipmentCollectionFactory
     */
    private $postnlCollectionFactory;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var Labelling
     */
    private $labelling;

    /**
     * @param Context                         $context
     * @param Filter                          $filter
     * @param ShipmentCollectionFactory       $collectionFactory
     * @param PostnlShipmentCollectionFactory $postnlCollectionFactory
     * @param FileFactory                     $fileFactory
     * @param LabelGenerator                  $labelGenerator
     * @param Labelling                       $labelling
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        PostnlShipmentCollectionFactory $postnlCollectionFactory,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        Labelling $labelling
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->postnlCollectionFactory = $postnlCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
        $this->labelling = $labelling;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        /**
         * TODO: Flow according to M1 version:
         *
         * 1. check if user is allowed to perform action > N/A
         * 2. check maximum label printing > Unsure if necessary
         * 3. set_time_limit(0) > Unsure if necessary
         * 4. check if checked shipments are PostNL shipments (if not, addWarning()) > Partly done, no warning is shown
         * 5. Confirm & get labels > Done
         * 5.a Confirming -> just confirm, no labels > Unsure if necessary
         * 5.b GenerateLabel -> both confirm and labels > Done
         * 5.c GenerateLabelWithoutConfirm -> just label, no confirm. Confirming webservice has to be used after this. > todo
         * 5.d in short, the api flow is either GenerateLabelWithoutConfirm -> Confirming or just GenerateLabel
         * 6. Check warnings/errors > todo
         * 7. Update shipment/order status > todo
         * 8. Create & print PDFs > done
         */

        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->currentShipment = $shipment;
            $this->getLabel();
        }

        return $this->outputPdf();
    }

    private function getLabel()
    {
        $collection = $this->postnlCollectionFactory->create();
        $collection->addFieldToFilter('shipment_id', array('eq' => $this->currentShipment->getId()));

        //TODO: add a proper warning notifying of a non-postnl shipment
        if (count($collection) < 0) {
            return;
        }

        /** @var \TIG\PostNL\Model\Shipment $postnlShipment */
        foreach ($collection as $postnlShipment) {
            //TODO: catch the error and notify the user of it
            $this->labels[] = $this->generateLabel($postnlShipment);
        }

        return;
    }

    /**
     * @param \TIG\PostNL\Model\Shipment $postnlShipment
     *
     * @return \Magento\Framework\Phrase
     */
    private function generateLabel($postnlShipment)
    {
        $this->labelling->setParameters($postnlShipment);
        $response = $this->labelling->call();

        if (!is_object($response) || !isset($response->ResponseShipments->ResponseShipment)) {
            return __('Invalid generateLabel response: %1', var_export($response, true));
        }

        return $response->ResponseShipments->ResponseShipment[0]->Labels->Label[0]->Content;
    }

    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    private function outputPdf()
    {
        /** @var \Zend_Pdf $combinedLabels */
        $combinedLabels = $this->labelGenerator->combineLabelsPdf($this->labels)->render();

        return $this->fileFactory->create(
            'ShippingLabels.pdf',
            $combinedLabels,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
