<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class MassPrintPackingslip extends LabelAbstract
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context                   $context
     * @param Filter                    $filter
     * @param ShipmentCollectionFactory $collectionFactory
     * @param GetLabels                 $getLabels
     * @param GetPdf                    $getPdf
     * @param Track                     $track
     * @param BarcodeHandler            $barcodeHandler
     * @param GetPackingslip            $getPackingSlip
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        GetLabels $getLabels,
        GetPdf $getPdf,
        Track $track,
        BarcodeHandler $barcodeHandler,
        GetPackingslip $getPackingSlip
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf,
            $getPackingSlip,
            $barcodeHandler,
            $track
        );

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->loadLabels();

        if (empty($this->labels)) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return $this->getPdf->get($this->labels, GetPdf::FILETYPE_PACKINGSLIP);
    }

    private function loadLabels()
    {
        $collection = $this->collectionFactory->create();

        try {
            $collection = $this->filter->getCollection($collection);
        } catch (LocalizedException $exception) {
            $this->messageManager->addWarningMessage($exception->getMessage());
            return;
        }

        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->loadLabel($shipment);
        }
    }

    /**
     * @param Shipment $shipment
     */
    private function loadLabel($shipment)
    {
        $address = $shipment->getShippingAddress();

        try {
            $this->barcodeHandler->prepareShipment($shipment->getId(), $address->getCountryId(), false);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(
                __('[POSTNL-0070] - Unable to generate barcode for shipment #%1', $shipment->getIncrementId())
            );
            return;
        }

        $this->setTracks($shipment);
        $this->setPackingslip($shipment->getId(), true, false);
    }
}
