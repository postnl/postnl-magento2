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

use \Magento\TestFramework\Helper\Bootstrap;
use \Magento\Sales\Model\Order\Payment;
use \Magento\Sales\Model\Order;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Sales\Model\Order\Address;
use \Magento\Sales\Model\Order\Item;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Model\Product\Type;

require __DIR__.'/../default_rollback.php';
require __DIR__.'/RegularProduct.php';

$address = include __DIR__.'/Address.php';

$objectManager = Bootstrap::getObjectManager();

/** @var Address $billingAddress */
$billingAddress = $objectManager->create(Address::class, ['data' => $address]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null);
$shippingAddress->setAddressType('shipping');

/** @var Item $orderItem */
$orderItem = $objectManager->create(Item::class);

/** @var Product $product */
$orderItem->setProductId($product->getId());
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setSku($product->getSku());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType(Type::TYPE_SIMPLE);
$orderItem->setWeight($product->getWeight());
$orderItem->setQtyOrdered(1);

/** @var Payment $payment */
$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');
$payment->setAdditionalInformation([
    'token_metadata' => [
        'token'       => '1337',
        'customer_id' => 1
    ]
]);

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100001337');

// State and status
$order->setState(Order::STATE_PROCESSING);
$status = $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING);
$order->setStatus($status);

// Order amounts
$order->setSubtotal(100);
$order->setGrandTotal(100);
$order->setBaseGrandTotal(100);
$order->setBaseSubtotal(100);

// is Guest customer data
$order->setCustomerIsGuest(true);
$order->setCustomerEmail('customer@tig.nl');
$order->setBillingAddress($billingAddress);
$order->setShippingAddress($shippingAddress);

// Set store ID
$storeId = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();
$order->setStoreId($storeId);

// Add the order item
$order->addItem($orderItem);

$order->setPayment($payment);

// Save Order
$order->save();