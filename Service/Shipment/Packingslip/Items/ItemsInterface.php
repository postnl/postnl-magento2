<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Items;

use \Magento\Sales\Api\Data\ShipmentInterface;

interface ItemsInterface
{
    /**
     * @param string  $packingSlip
     * @param ShipmentInterface $shipment
     *
     * @return string
     */
    public function add($packingSlip, $shipment);
}
