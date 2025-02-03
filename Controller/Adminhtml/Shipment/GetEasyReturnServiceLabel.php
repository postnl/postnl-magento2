<?php
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\ErsCountries;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Service\Shipment\ErsShipmentManager;

class GetEasyReturnServiceLabel extends LabelAbstract
{
    private ShipmentRepository $shipmentRepository;

    private ErsShipmentManager $ersShipmentManager;

    public function __construct(
        Context                     $context,
        GetLabels                   $getLabels,
        GetPdf                      $getPdf,
        ShipmentRepository          $shipmentRepository,
        Track                       $track,
        BarcodeHandler              $barcodeHandler,
        GetPackingslip              $getPackingSlip,
        ErsShipmentManager          $ersShipmentManager
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
        $this->ersShipmentManager = $ersShipmentManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');

        $magentoShipment = $this->shipmentRepository->get($shipmentId);
        $countryId = $magentoShipment->getShippingAddress()->getCountryId();

        if (!ErsCountries::isIncluded($countryId)) {
            $this->messageManager->addErrorMessage(
                __('Easy Return Service is not available for this country.')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $labels = $this->ersShipmentManager->processShipmentLabel($magentoShipment);
            $this->messageManager->addSuccessMessage(__('Successfully sent out all Easy Return Service labels'));
            return $this->getPdf->get($labels, 'EasyReturnService');
        } catch (Exception|LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
