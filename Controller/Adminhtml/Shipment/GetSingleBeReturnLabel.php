<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\Generate\SingleBeReturn;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class GetSingleBeReturnLabel extends LabelAbstract
{
    private ShipmentRepository $shipmentRepository;

    private SingleBeReturn $labelRequest;
    private ShipmentRepositoryInterface $postnlShipmentRepository;

    public function __construct(
        Context                    $context,
        GetLabels                  $getLabels,
        GetPdf                     $getPdf,
        GetPackingslip             $getPackingSlip,
        BarcodeHandler             $barcodeHandler,
        Track                      $track,
        ShipmentRepository         $shipmentRepository,
        SingleBeReturn             $labelRequest,
        ShipmentRepositoryInterface $postnlShipmentRepository
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
        $this->labelRequest = $labelRequest;
        $this->postnlShipmentRepository = $postnlShipmentRepository;
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

        if ($countryId !== 'BE') {
            $this->messageManager->addErrorMessage(
                __('Single Return Label is only available for BE shipments.')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $postnlShipment = $this->postnlShipmentRepository->getByShipmentId($magentoShipment->getId());
            if (!$postnlShipment->getConfirmed() && !$postnlShipment->getMainBarcode()) {
                throw new LocalizedException(__('Returns are only active after main barcode is generated.'));
            }
            $labels = $this->labelRequest->get($postnlShipment, 1);
            if (!$labels) {
                throw new LocalizedException(__('Unable to generate return label, please check logs.'));
            }
            return $this->getPdf->get($labels, 'ReturnLabel');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
