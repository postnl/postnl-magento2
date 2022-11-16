<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Service\Shipment\CreateShipment;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Helper\Tracking\Track;
use \Magento\Sales\Model\Order\Shipment;

class CreateShipmentsAndPrintPackingSlip extends LabelAbstract
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Order
     */
    private $currentOrder;

    /**
     * @var CreateShipment
     */
    private $createShipment;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param Context                $context
     * @param Filter                 $filter
     * @param GetLabels              $getLabels
     * @param GetPdf                 $getPdf
     * @param OrderCollectionFactory $collectionFactory
     * @param ConvertOrder           $convertOrder
     * @param CreateShipment         $createShipment
     * @param GetPackingslip         $getPackingSlip
     * @param BarcodeHandler         $barcodeHandler
     * @param Track                  $track
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        CreateShipment $createShipment,
        ConvertOrder $convertOrder,
        GetPackingslip $getPackingSlip,
        BarcodeHandler $barcodeHandler,
        Track $track
    ) {
        parent::__construct($context, $getLabels, $getPdf, $getPackingSlip, $barcodeHandler, $track);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->convertOrder = $convertOrder;
        $this->createShipment = $createShipment;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Zend_Pdf_Exception
     */
    public function execute()
    {
        $this->createShipmentsAndLoadPackingSlips();
        $this->handleErrors();

        if (empty($this->labels)) {
            $this->messageManager->addErrorMessage(
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return $this->getPdf->get($this->labels, GetPdf::FILETYPE_PACKINGSLIP);
    }

    /**
     * Load the shipments and labels
     */
    private function createShipmentsAndLoadPackingSlips()
    {
        $collection = $this->collectionFactory->create();

        try {
            $collection = $this->filter->getCollection($collection);
        } catch (LocalizedException $exception) {
            $this->messageManager->addWarningMessage($exception->getMessage());
            return;
        }

        /** @var Order $order */
        foreach ($collection as $order) {
            $this->currentOrder = $order;
            $shipment = $this->createShipment->create($order);
            $this->loadLabels($shipment);
        }
    }

    /**
     * @return $this
     */
    private function handleErrors()
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        $shipmentErrors = $this->createShipment->getErrors();
        foreach ($shipmentErrors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        return $this;
    }

    /**
     * @param Shipment $shipment
     */
    private function loadLabels($shipment)
    {
        if (!$shipment) {
            return;
        }

        if (is_array($shipment)) {
            $shipment = array_pop($shipment);
        }

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
