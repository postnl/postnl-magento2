<?php

namespace TIG\PostNL\Service\Parcel\Order;

use Magento\Sales\Model\Order;
use TIG\PostNL\Service\Parcel\CountAbstract;

class Count extends CountAbstract
{
    /**
     * @param Order $order
     *
     * @return int|float
     */
    public function get(Order $order)
    {
        $items = $order->getItems();
        $shipping = $order->getShippingAddress();
        $countryId = $shipping ? $shipping->getCountryId() : '';
        // Only domestic shipments allowed for several packages
        if ($countryId !== 'NL' && $countryId !== 'BE') {
            return 1;
        }
        return $this->calculate($order->getWeight(), $items);
    }
}
