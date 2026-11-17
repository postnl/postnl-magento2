<?php

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;
use TIG\PostNL\Model\Shipment;

require __DIR__ . '/Shipment.php';
/** @var ObjectManagerInterface $objectManager */
/** @var MagentoShipment $shipment */

/** @var Shipment $postnlShipment */
$postnlShipment = $objectManager->create(Shipment::class);

$postnlShipment->setShipmentId($shipment->getId());
$postnlShipment->setOrderId($shipment->getOrderId());
$postnlShipment->setProductCode('3085');
$postnlShipment->setShipmentType('DayTime');

$objectManager->get(\TIG\PostNL\Api\ShipmentRepositoryInterface::class)->save($postnlShipment);

return $postnlShipment;
