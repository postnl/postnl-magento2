<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Service\Shipment\SmartReturnShipmentManager;

class GetSmartReturnLabel extends LabelAbstract
{
    private ShipmentRepository $shipmentRepository;

    private SmartReturnShipmentManager $smartReturnShipmentManager;

    public function __construct(
        Context                     $context,
        GetLabels                   $getLabels,
        GetPdf                      $getPdf,
        ShipmentRepository          $shipmentRepository,
        Track                       $track,
        BarcodeHandler              $barcodeHandler,
        GetPackingslip              $getPackingSlip,
        SmartReturnShipmentManager  $smartReturnShipmentManager,
    )
    {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf,
            $getPackingSlip,
            $barcodeHandler,
            $track
        );

        $this->shipmentRepository = $shipmentRepository;
        $this->smartReturnShipmentManager = $smartReturnShipmentManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $magentoShipment = $this->getShipment();
        $countryId = $magentoShipment->getShippingAddress()->getCountryId();

        if ($countryId !== 'NL') {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('Smart Returns is only available for NL shipments.')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $this->smartReturnShipmentManager->processShipmentLabel($magentoShipment);
            $this->messageManager->addSuccessMessage(__('Successfully sent out all Smart Return labels'));
        } catch (Exception|LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShipment(): ShipmentInterface
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');

        return $this->shipmentRepository->get($shipmentId);
    }
}
