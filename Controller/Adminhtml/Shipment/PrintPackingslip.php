<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;

use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class PrintPackingslip extends LabelAbstract
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @param Context            $context
     * @param GetLabels          $getLabels
     * @param GetPdf             $getPdf
     * @param ShipmentRepository $shipmentRepository
     * @param Track              $track
     * @param BarcodeHandler     $barcodeHandler
     * @param GetPackingslip     $getPackingSlip
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        ShipmentRepository $shipmentRepository,
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

        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->getPackingslip();

        if (empty($this->labels)) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return $this->getPdf->get($this->labels, GetPdf::FILETYPE_PACKINGSLIP);
    }

    private function getPackingslip()
    {
        $shipment = $this->getShipment();
        $address  = $shipment->getShippingAddress();

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

    /**
     * Retrieve shipment model instance
     *
     * @return ShipmentInterface|Shipment
     */
    private function getShipment()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        return $this->shipmentRepository->get($shipmentId);
    }
}
