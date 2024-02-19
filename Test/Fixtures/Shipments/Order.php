<?php

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;

require __DIR__ . '/Product.php';
/** @var ObjectManagerInterface $objectManager */
/** @var Product $product */

$addressData = include __DIR__ . '/Address.php';

$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

/** @var Payment $payment */
$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo')
    ->setAdditionalInformation([
        'token_metadata' => [
            'token' => '12345',
            'customer_id' => 1
        ]
    ]);

/** @var Item $orderItem */
$orderItem = $objectManager->create(Item::class);
$orderItem->setProductId($product->getId());
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setSku($product->getSku());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType(Type::TYPE_SIMPLE);
$orderItem->setWeight($product->getWeight());
$orderItem->setQtyOrdered(1);

/** @var Order $order */
$order = $objectManager->create(Order::class);

$processingStatus = $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING);
$storeId = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();

$order->setState(Order::STATE_PROCESSING);
$order->setStatus($processingStatus);
$order->setSubtotal(100);
$order->setGrandTotal(100);
$order->setBaseSubtotal(100);
$order->setBaseGrandTotal(100);
$order->setCustomerIsGuest(true);
$order->setCustomerEmail('order@tig.nl');
$order->setBillingAddress($billingAddress);
$order->setShippingAddress($shippingAddress);
$order->setStoreId($storeId);
$order->addItem($orderItem);
$order->setPayment($payment);

$order->save();
