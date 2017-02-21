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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

use TIG\PostNL\Helper\Labelling\GetLabels;
use TIG\PostNL\Helper\Labelling\SaveLabels;
use TIG\PostNL\Helper\Pdf\Get as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;

/**
 * Class MassPrintShippingLabel
 *
 * @package TIG\PostNL\Controller\Adminhtml\Shipment
 */
class MassPrintShippingLabel extends LabelAbstract
{
    /**
     * @var array
     */
    private $labels = [];

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Track
     */
    private $track;

    /**
     * @param Context                   $context
     * @param Filter                    $filter
     * @param ShipmentCollectionFactory $collectionFactory
     * @param GetLabels                 $getLabels
     * @param SaveLabels                $saveLabels
     * @param GetPdf                    $getPdf
     * @param Track                     $track
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        GetLabels $getLabels,
        SaveLabels $saveLabels,
        GetPdf $getPdf,
        Track $track
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $saveLabels,
            $getPdf
        );

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->track = $track;
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
            $this->track->set($shipment);
            $this->setLabel($shipment->getId());
        }

        $labelModels = $this->saveLabels->save($this->labels);

        $pdfFile = $this->getPdf->get($labelModels);

        return $pdfFile;
    }
    /**
     * @param $shipmentId
     */
    private function setLabel($shipmentId)
    {
        $labels = $this->getLabels->get($shipmentId);

        /**
         * @codingStandardsIgnoreLine
         * TODO: add a proper warning notifying of a non-postnl shipment
         */
        if (count($labels) < 0) {
            return;
        }

        $this->labels = $this->labels + $labels;
    }
}
