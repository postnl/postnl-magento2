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
$shipment->addItem($shipmentItem);
$shipment->setShipmentStatus(Shipment::STATUS_NEW);

$shipment->save();

return $shipment;
