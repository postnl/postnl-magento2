<?php

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
