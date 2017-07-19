<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;

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
     * @var BarcodeHandler
     */
    private $barcodeHandler;

    /**
     * @param Context                   $context
     * @param Filter                    $filter
     * @param ShipmentCollectionFactory $collectionFactory
     * @param GetLabels                 $getLabels
     * @param GetPdf                    $getPdf
     * @param Track                     $track
     * @param BarcodeHandler            $barcodeHandler
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        GetLabels $getLabels,
        GetPdf $getPdf,
        Track $track,
        BarcodeHandler $barcodeHandler
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf
        );

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->track = $track;
        $this->barcodeHandler = $barcodeHandler;
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
        $this->loadLabels($collection);

        if (empty($this->labels)) {
            $this->messageManager->addErrorMessage(
                // @codingStandardsIgnoreLine
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return $this->getPdf->get($this->labels);
    }

    /**
     * @param $shipmentId
     */
    private function setLabel($shipmentId)
    {
        $labels = $this->getLabels->get($shipmentId);

        if (empty($labels)) {
            return;
        }

        $this->labels = array_merge($this->labels, $labels);
    }

    /**
     * @param Shipment $shipment
     */
    private function setTracks($shipment)
    {
        if (!$shipment->getTracks()) {
            $this->track->set($shipment);
        }
    }

    /**
     * @param $collection
     */
    private function loadLabels($collection)
    {
        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $address = $shipment->getShippingAddress();
            $this->barcodeHandler->prepareShipment($shipment->getId(), $address->getCountryId());
            $this->setTracks($shipment);
            $this->setLabel($shipment->getId());
        }
    }
}
