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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
namespace TIG\PostNL\Service\Shipment;

use Magento\InventoryShipping\Plugin\Sales\Shipment\AssignSourceCodeToShipmentPlugin;
use Magento\Sales\Model\Order\ShipmentFactory;
use TIG\PostNL\Service\Shipment\InventorySource\Factory;

class InventorySource
{
    /**
     * @var AssignSourceCodeToShipmentPlugin
     */
    private $inventorySourceFactory;

    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * InventorySource constructor.
     *
     * @param ShipmentFactory $shipmentFactory
     * @param Factory         $inventorySourceFactory
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        Factory $inventorySourceFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->inventorySourceFactory = $inventorySourceFactory;
    }

    public function setSource($shipment, $order)
    {
        $inventorySourceFactory = $this->inventorySourceFactory->create();
        
        if ($inventorySourceFactory) {
            $shipment = $this->inventorySourceFactory->afterCreate($this->shipmentFactory, $shipment, $order);
        }

        return $shipment;
    }
}
