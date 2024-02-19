<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\Order\Email;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Api\ShipmentManagement;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class MassGetSmartReturnLabel extends LabelAbstract
{
    /** @var Email  */
    private $email;

    /** @var ShipmentManagement  */
    private $shipmentManagement;

    /** @var ShipmentCollectionFactory  */
    private $collectionFactory;

    /** @var Filter  */
    private $filter;

    /** @var ShipmentRepositoryInterface  */
    private $shipmentRepository;

    /**
     * GetSmartReturnLabel constructor.
     *
     * @param Context                          $context
     * @param GetLabels                        $getLabels
     * @param GetPdf                           $getPdf
     * @param Track                            $track
     * @param BarcodeHandler                   $barcodeHandler
     * @param GetPackingslip                   $getPackingSlip
     * @param Email                            $email
     * @param ShipmentManagement               $shipmentManagement
     * @param ShipmentCollectionFactory        $collectionFactory
     * @param Filter                           $filter
     * @param ShipmentRepositoryInterface      $shipmentRepository
     */
    public function __construct(
        Context                          $context,
        GetLabels                        $getLabels,
        GetPdf                           $getPdf,
        Track                            $track,
        BarcodeHandler                   $barcodeHandler,
        GetPackingslip                   $getPackingSlip,
        Email                            $email,
        ShipmentManagement               $shipmentManagement,
        ShipmentCollectionFactory        $collectionFactory,
        Filter                           $filter,
        ShipmentRepositoryInterface      $shipmentRepository
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf,
            $getPackingSlip,
            $barcodeHandler,
            $track
        );

        $this->email              = $email;
        $this->shipmentManagement = $shipmentManagement;
        $this->collectionFactory  = $collectionFactory;
        $this->filter             = $filter;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $magentoShipments = $this->getShipment();

        foreach ($magentoShipments as $magentoShipment) {
            $countryId = $magentoShipment->getShippingAddress()->getCountryId();

            if ($countryId !== 'NL') {
                $this->messageManager->addErrorMessage(
                // @codingStandardsIgnoreLine
                    __('Smart Returns is only available for NL shipments.')
                );

                return $this->_redirect($this->_redirect->getRefererUrl());
            }
        }

        foreach ($magentoShipments as $magentoShipment) {
            $this->shipmentManagement->generateLabel($magentoShipment->getId(), true);
            $labels = $this->getLabels->get($magentoShipment->getId(), false);

            if (empty($labels)) {
                $this->messageManager->addErrorMessage(
                // @codingStandardsIgnoreLine
                    __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
                );

                return $this->_redirect($this->_redirect->getRefererUrl());
            }

            $this->email->sendEmail($magentoShipment, $labels);

			// set smart return email sent true
			$postnlShipment = $this->shipmentRepository->getByShipmentId($magentoShipment->getId());
			$postnlShipment->setSmartReturnEmailSent(true);
			$this->shipmentRepository->save($postnlShipment);
        }

        $this->messageManager->addSuccessMessage(__('Successfully send out all Smart Return labels'));

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShipment()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        return $collection;
    }
}
