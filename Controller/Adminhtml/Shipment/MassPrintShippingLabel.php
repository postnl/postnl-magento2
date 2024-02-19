<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Framework\Exception\LocalizedException;
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
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class MassPrintShippingLabel extends LabelAbstract
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
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Zend_Pdf_Exception
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

        return $this->getPdf->get($this->labels);
    }

    /**
     * Load the labels for the shipments
     */
    private function loadLabels()
    {
        $collection = $this->collectionFactory->create();

        try {
            $collection = $this->filter->getCollection($collection);
        } catch (LocalizedException $exception) {
            $this->messageManager->addWarningMessage($exception->getMessage());
            return;
        }

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
        $this->setLabel($shipment->getId());
    }
}
