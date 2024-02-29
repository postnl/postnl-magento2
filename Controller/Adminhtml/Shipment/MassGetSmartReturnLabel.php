<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Service\Shipment\SmartReturnShipmentManager;

class MassGetSmartReturnLabel extends LabelAbstract
{
    /** @var ShipmentCollectionFactory  */
    private $collectionFactory;

    /** @var Filter  */
    private $filter;

    private SmartReturnShipmentManager $smartReturnShipmentManager;

    /**
     * GetSmartReturnLabel constructor.
     *
     * @param Context                          $context
     * @param GetLabels                        $getLabels
     * @param GetPdf                           $getPdf
     * @param Track                            $track
     * @param BarcodeHandler                   $barcodeHandler
     * @param GetPackingslip                   $getPackingSlip
     * @param ShipmentCollectionFactory        $collectionFactory
     * @param Filter                           $filter
     * @param SmartReturnShipmentManager $smartReturnShipmentManager
     */
    public function __construct(
        Context                          $context,
        GetLabels                        $getLabels,
        GetPdf                           $getPdf,
        Track                            $track,
        BarcodeHandler                   $barcodeHandler,
        GetPackingslip                   $getPackingSlip,
        ShipmentCollectionFactory        $collectionFactory,
        Filter                           $filter,
        SmartReturnShipmentManager       $smartReturnShipmentManager
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf,
            $getPackingSlip,
            $barcodeHandler,
            $track
        );

        $this->collectionFactory  = $collectionFactory;
        $this->filter             = $filter;
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
            try {
                $this->smartReturnShipmentManager->processShipmentLabel($magentoShipment);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    $e->getMessage()
                );
                return $this->_redirect($this->_redirect->getRefererUrl());
            }
        }

        $this->messageManager->addSuccessMessage(__('Successfully sent out all Smart Return labels'));

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
