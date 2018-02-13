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

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;

class CreateShipmentsConfirmAndPrintShippingLabels extends LabelAbstract
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * @var Track
     */
    private $track;

    /**
     * @var BarcodeHandler
     */
    private $barcodeHandler;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var Order
     */
    private $currentOrder;

    /**
     * @var array
     */
    private $labels = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param Context                $context
     * @param GetLabels              $getLabels
     * @param GetPdf                 $getPdf
     * @param Filter                 $filter
     * @param OrderCollectionFactory $collectionFactory
     * @param ConvertOrder           $convertOrder
     * @param Track                  $track
     * @param BarcodeHandler         $barcodeHandler
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        ConvertOrder $convertOrder,
        Track $track,
        BarcodeHandler $barcodeHandler
    ) {
        parent::__construct($context, $getLabels, $getPdf);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->convertOrder = $convertOrder;
        $this->track = $track;
        $this->barcodeHandler = $barcodeHandler;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        /** @var Order $order */
        foreach ($collection as $order) {
            $this->currentOrder = $order;
            $this->createShipment();
            $this->loadLabel();
        }

        $this->handleErrors();

        return $this->getPdf->get($this->labels);
//        return $this->redirectBack();
    }

    private function loadLabel()
    {
        $address = $this->shipment->getShippingAddress();
        $this->barcodeHandler->prepareShipment($this->shipment->getId(), $address->getCountryId());

        if (!$this->shipment->getTracks()) {
            $this->track->set($this->shipment);
        }

        $this->setLabel($this->shipment->getId());
    }

    /**
     * @param $shipmentId
     */
    private function setLabel($shipmentId)
    {
        $labels = $this->getLabels->get($shipmentId);

        if (empty($labels)) {
            return;
        }

        $this->labels = array_merge($this->labels, $labels);
    }

    /**
     * @return $this
     */
    private function createShipment()
    {
        if (!$this->isValidOrder()) {
            return $this;
        }

        $this->shipment = $this->convertOrder->toShipment($this->currentOrder);

        /** @var OrderItem $item */
        foreach ($this->currentOrder->getAllItems() as $item) {
            $this->handleItem($item);
        }

        $this->saveShipment();

        return $this;
    }

    /**
     * @return bool
     */
    private function isValidOrder()
    {
        if ($this->orderHasShipment()) {
            return false;
        }

        if (!$this->currentOrder->canShip()) {
            return false;
        }

        if ($this->currentOrder->getShippingMethod() !== 'tig_postnl_regular') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function orderHasShipment()
    {
        $collection = $this->currentOrder->getShipmentsCollection();
        $size = $collection->getSize();

        return $size !== 0;
    }

    /**
     * @param OrderItem $item
     *
     * @return $this
     */
    private function handleItem(OrderItem $item)
    {
        if (!$item->getQtyToShip() || $item->getIsVirtual()) {
            return $this;
        }

        $qtyShipped = $item->getQtyToShip();

        $shipmentItem = $this->convertOrder->itemToShipmentItem($item);
        $shipmentItem->setQty($qtyShipped);

        $this->shipment->addItem($shipmentItem);

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveShipment()
    {
        $this->shipment->register();
        $order = $this->shipment->getOrder();
        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus('processing');

        try {
            $this->shipment->save();
            $order->save();
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $localizedErrorMessage = __($message)->render();
            $this->errors[] = $localizedErrorMessage;
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function handleErrors()
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectBack()
    {
        $redirectPath = 'sales/shipment/index';
        if (!empty($this->errors)) {
            $redirectPath = 'sales/order/index';
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setPath($redirectPath);

        return $resultRedirect;
    }
}
