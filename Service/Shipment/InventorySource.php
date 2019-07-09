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

use Magento\Sales\Model\Order\ShipmentFactory;

class InventorySource
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * InventorySource constructor.
     *
     * @param ShipmentFactory $shipmentFactory
     */
    public function __construct(
        ShipmentFactory $shipmentFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * Magento uses an afterCreate plugin on the shipmentFactory to set the SourceCode. In the default flow Magento
     * runs this code when you open the Create Shipment page. This behaviour doesn't occur in this flow, so we force
     * that flow to happen here.
     *
     * @param $order
     * @param $shipmentItems
     *
     * @return Shipment
     */
    public function getSource($order, $shipmentItems)
    {
        /** @var Shipment $shipment */
        $shipment = $this->shipmentFactory->create(
            $order,
            $shipmentItems
        );

        $extensionAttributes = $shipment->getExtensionAttributes();

        return $extensionAttributes->getSourceCode();
    }
}
