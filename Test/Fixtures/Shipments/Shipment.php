<?php

use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item as ShipmentItem;

require __DIR__ . '/Order.php';
/** @var ObjectManagerInterface $objectManager */
/** @var Order $order */
/** @var Item $orderItem */

$payment = $order->getPayment();
$paymentInfoBlock = $objectManager->get(Data::class)
    ->getInfoBlock($payment);
$payment->setBlockMock($paymentInfoBlock);

/** @var Shipment $shipment */
$shipment = $objectManager->create(Shipment::class);
$shipment->setOrder($order);

$shipmentItem = $objectManager->create(ShipmentItem::class);
$shipmentItem->setOrderItem($orderItem);
$shipmentItem->setProductId($orderItem->getProductId());
$shipmentItem->setPrice($orderItem->getPrice());
$shipmentItem->setSku($orderItem->getSku());
$shipmentItem->setWeight($orderItem->getWeight());
$shipmentItem->setQty($orderItem->getQtyToShip());
$shipment->addItem($shipmentItem);
$shipment->setShipmentStatus(Shipment::STATUS_NEW);

$shipment->save();

return $shipment;
