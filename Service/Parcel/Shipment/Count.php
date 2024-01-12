<?php

namespace TIG\PostNL\Service\Parcel\Shipment;

use \TIG\PostNL\Service\Parcel\CountAbstract;
use Magento\Sales\Api\Data\ShipmentInterface;

class Count extends CountAbstract
{
    /**
     * @param ShipmentInterface $shipment
     *
     * @return int|\Magento\Framework\Api\AttributeInterface|null
     */
    public function get(ShipmentInterface $shipment)
    {
        return $this->calculate($shipment->getTotalWeight(), $shipment->getItems());
    }
}
