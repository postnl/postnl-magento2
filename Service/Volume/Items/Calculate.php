<?php

namespace TIG\PostNL\Service\Volume\Items;

use TIG\PostNL\Service\Volume\CalculateAbstract;
use \Magento\Sales\Api\Data\ShipmentItemInterface;

class Calculate extends CalculateAbstract
{
    /**
     * @param ShipmentItemInterface[] $items
     *
     * @return float|int
     */
    public function get($items)
    {
        return $this->calculate($items);
    }
}
