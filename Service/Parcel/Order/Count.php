<?php

namespace TIG\PostNL\Service\Parcel\Order;

use \TIG\PostNL\Service\Parcel\CountAbstract;
use \Magento\Sales\Api\Data\OrderInterface;

class Count extends CountAbstract
{
    /**
     * @param OrderInterface $order
     *
     * @return int|\Magento\Framework\Api\AttributeInterface|null
     */
    public function get(OrderInterface $order)
    {
        $items = $order->getItems();
        return $this->calculate($order->getWeight(), $items);
    }
}
