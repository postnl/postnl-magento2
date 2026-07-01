<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Order;

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;

class ShipAt
{
    public function __construct(
        private readonly QuoteInterface $quote,
        private readonly DeliveryDateFallback $deliveryDateFallback
    ) {
    }

    /**
     * @return OrderInterface|null
     */
    public function set(OrderInterface $order)
    {
        $address = $this->quote->getShippingAddress();

        if (!$address || !$order) {
            return null;
        }

        $order->setShipAt($this->deliveryDateFallback->getShipAtDate());

        return $order;
    }
}
