<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class PrintShippingLabel extends LabelAbstract
{
    /** @var ShipmentRepository */
    private $shipmentRepository;

    /**
     * PrintShippingLabel constructor.
     *
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\Message\ManagerInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Pdf_Exception
     */
    public function execute()
    {
        $labels = $this->getLabels();

        if (empty($labels)) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return $this->getPdf->get($labels);
    }

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShipment()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        return $this->shipmentRepository->get($shipmentId);
    }

    /**
     * @return array|\Magento\Framework\Phrase|string|\TIG\PostNL\Api\Data\ShipmentLabelInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getLabels()
    {
        $shipment = $this->getShipment();
        $shippingAddress = $shipment->getShippingAddress();

        try {
            $this->barcodeHandler->prepareShipment($shipment->getId(), $shippingAddress->getCountryId(),false);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(
                __('[POSTNL-0070] - Unable to generate barcode for shipment #%1', $shipment->getIncrementId())
            );
            return [];
        }

        $labels = $this->getLabels->get($shipment->getId(), false);

        return $labels;
    }
}
