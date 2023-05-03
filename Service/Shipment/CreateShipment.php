<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Model\ShipmentRepository;

//@codingStandardsIgnoreFile
class CreateShipment
{
    /**
     * @var InventorySource
     */
    private $inventorySource;

    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var Order
     */
    private $currentOrder;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var Data
     */
    private $postNLHelper;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param ConvertOrder       $convertOrder
     * @param ShipmentRepository $shipmentRepository
     * @param Data               $postnlHelper
     * @param InventorySource    $inventorySource
     */
    public function __construct(
        ConvertOrder $convertOrder,
        ShipmentRepository $shipmentRepository,
        Data $postnlHelper,
        InventorySource $inventorySource
    ) {
        $this->convertOrder       = $convertOrder;
        $this->shipmentRepository = $shipmentRepository;
        $this->postNLHelper       = $postnlHelper;
        $this->inventorySource    = $inventorySource;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Order $order
     *
     * @return Shipment|null|array
     */
    public function create(Order $order)
    {
        $this->shipment = [];
        $this->currentOrder = $order;

        $foundShipment = $this->findPostNLShipment();
        if ($foundShipment) {
            return $foundShipment;
        }

        if (!$this->isValidOrder()) {
            return null;
        }

        $this->createShipment();

        return $this->shipment;
    }

    /**
     * Create the actual shipment
     */
    private function createShipment()
    {
        $this->shipment = $this->convertOrder->toShipment($this->currentOrder);

        foreach ($this->currentOrder->getAllItems() as $item) {
            $this->handleItem($item);
        }

        $this->saveShipment();
    }

    /**
     * Look if an order already has a PostNL shipment. If so, return that shipment.
     * @codingStandardsIgnoreLine
     *
     * @return array
     */
    private function findPostNLShipment()
    {
        $collection = $this->currentOrder->getShipmentsCollection();
        $shipments = $collection->getItems();
        $foundShipment = [];

        array_walk(
            $shipments,
            function ($item) use (&$foundShipment) {
                /** @var Shipment $item */
                $postnlShipment = $this->shipmentRepository->getByShipmentId($item->getId());

                if ($postnlShipment) {
                    array_push($foundShipment, $item);
                }
            }
        );

        return $foundShipment;
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

        if (!$this->postNLHelper->isPostNLOrder($this->currentOrder)) {
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

        $shipmentAttributes = $this->shipment->getExtensionAttributes();

        // This method only exists if you have the various Magento Inventory extensions installed.
        if (method_exists($shipmentAttributes, 'setSourceCode')) {
            $shipmentAttributes->setSourceCode($this->inventorySource->getSource($order, $this->getShippingItems()));
            $this->shipment->setExtensionAttributes($shipmentAttributes);
        }

        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus('processing');

        try {
            $this->shipment->save();
            $order->save();
        } catch (\Exception $exception) {
            $message = $this->handleExceptionForPossibleSoapErrors($exception);
            $localizedErrorMessage = __($message)->render();
            $this->errors[] = $localizedErrorMessage;
            $this->shipment = false;
        }

        return $this;
    }

    private function getShippingItems()
    {
        $shippingItems = [];

        foreach ($this->shipment->getItems() as $shipmentItem) {
            $shippingItems[$shipmentItem->getProductId()] = (string)$shipmentItem->getQty();
        }

        return $shippingItems;
    }

    /**
     * @param \Exception $exception
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function handleExceptionForPossibleSoapErrors(\Exception $exception)
    {
        if (!method_exists($exception, 'getErrors') || !$exception->getErrors() || !is_array($exception->getErrors())) {
            return $exception->getMessage();
        }

        $message =  __('[POSTNL-0010] - An error occurred while processing this action.');
        foreach ($exception->getErrors() as $error) {
            $message .= ' ' . (string) $error;
        }

        return $message;
    }
}
