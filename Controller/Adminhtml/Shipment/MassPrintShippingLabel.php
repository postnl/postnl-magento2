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
use TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory as ShipmentLabelCollectionFactory;
use TIG\PostNL\Model\ShipmentLabel;
use TIG\PostNL\Model\ShipmentLabelFactory;
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
     * @var ShipmentLabelFactory
     */
    private $shipmentLabelFactory;

    /**
     * @var ShipmentLabelCollectionFactory
     */
    private $shipmentLabelCollectionFactory;

    /**
     * @param Context                         $context
     * @param Filter                          $filter
     * @param ShipmentCollectionFactory       $collectionFactory
     * @param PostnlShipmentCollectionFactory $postnlCollectionFactory
     * @param FileFactory                     $fileFactory
     * @param LabelGenerator                  $labelGenerator
     * @param Labelling                       $labelling
     * @param ShipmentLabelFactory            $shipmentLabelFactory
     * @param ShipmentLabelCollectionFactory  $shipmentLabelCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        PostnlShipmentCollectionFactory $postnlCollectionFactory,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        Labelling $labelling,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelCollectionFactory $shipmentLabelCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->postnlCollectionFactory = $postnlCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
        $this->labelling = $labelling;
        $this->shipmentLabelFactory = $shipmentLabelFactory;
        $this->shipmentLabelCollectionFactory = $shipmentLabelCollectionFactory;
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

        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->currentShipment = $shipment;
            $this->getLabel();
        }

        $this->checkWarnings();

        $this->updateStatus();

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
            $this->labels[$postnlShipment->getId()] = $this->generateLabel($postnlShipment);
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

    private function checkWarnings()
    {
        //TODO: Notify the user of the warning
        array_filter(
            $this->labels,
            function ($value) {
                return (is_string($value));
            }
        );
    }

    private function updateStatus()
    {
        $deliveryTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $deliveryTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $deliveryDate = $deliveryTime->format('Y-m-d');

        $shipmentIds = array_keys($this->labels);

        /** @var \TIG\PostNL\Model\ResourceModel\Shipment\Collection $collection */
        $collection = $this->postnlCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', array('in' => $shipmentIds));
        $collection->setDataToAll('confirmed_at', $deliveryDate);
        $collection->save();
    }

    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    private function outputPdf()
    {
        /** @var \TIG\PostNL\Model\ResourceModel\ShipmentLabel\Collection $labelModelCollection */
        $labelModelCollection = $this->shipmentLabelCollectionFactory->create();
        $labelModelCollection->load();

        foreach ($this->labels as $shipmentId => $label) {
            /** @var ShipmentLabel $labelModel */
            $labelModel = $this->shipmentLabelFactory->create();
            $labelModel->setParentId($shipmentId);
            $labelModel->setLabel(base64_encode($label));
            $labelModel->setType(ShipmentLabel::BARCODE_TYPE_LABEL);

            $labelModelCollection->addItem($labelModel);
        }

        $labelModelCollection->save();

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
