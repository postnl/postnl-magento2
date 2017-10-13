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

$postnlShipment->save();
