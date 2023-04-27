<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\Order\Email;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Api\ShipmentManagement;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class GetSmartReturnLabel extends LabelAbstract
{
    /** @var ShipmentRepository */
    private $shipmentRepository;

    /** @var Email  */
    private $email;

    /** @var ShipmentManagement  */
    private $shipmentManagement;

    /** @var ShipmentRepositoryInterface  */
    private $shipmentRepositoryInterface;

    /**
     * GetSmartReturnLabel constructor.
     *
     * @param Context                          $context
     * @param GetLabels                        $getLabels
     * @param GetPdf                           $getPdf
     * @param ShipmentRepository               $shipmentRepository
     * @param Track                            $track
     * @param BarcodeHandler                   $barcodeHandler
     * @param GetPackingslip                   $getPackingSlip
     * @param Email                            $email
     * @param ShipmentManagement               $shipmentManagement
     * @param ShipmentLabelRepositoryInterface $shipmentLabel
     * @param ShipmentRepositoryInterface      $shipmentRepositoryInterface
     */
    public function __construct(
        Context                          $context,
        GetLabels                        $getLabels,
        GetPdf                           $getPdf,
        ShipmentRepository               $shipmentRepository,
        Track                            $track,
        BarcodeHandler                   $barcodeHandler,
        GetPackingslip                   $getPackingSlip,
        Email                            $email,
        ShipmentManagement               $shipmentManagement,
        ShipmentRepositoryInterface      $shipmentRepositoryInterface
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf,
            $getPackingSlip,
            $barcodeHandler,
            $track
        );

        $this->shipmentRepository          = $shipmentRepository;
        $this->email                       = $email;
        $this->shipmentManagement          = $shipmentManagement;
        $this->shipmentRepositoryInterface = $shipmentRepositoryInterface;
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
        $countryId       = $magentoShipment->getShippingAddress()->getCountryId();

        if ($countryId !== 'NL') {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('Smart Returns is only available for NL shipments.')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        $this->shipmentManagement->generateLabel($magentoShipment->getId(), true);
        $labels = $this->getLabels->get($magentoShipment->getId(), false);

        if (empty($labels)) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $this->email->sendEmail($magentoShipment, $labels);
            // set smart return email sent true
            $postnlShipment = $this->shipmentRepositoryInterface->getByShipmentId($magentoShipment->getId());
            $postnlShipment->setSmartReturnEmailSent(true);
            $this->shipmentRepositoryInterface->save($postnlShipment);

            $this->messageManager->addSuccessMessage(__('Successfully send out all Smart Return labels'));
        } catch (Exception $e){
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
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
}
