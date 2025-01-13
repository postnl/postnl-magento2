<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;

class DefaultTab implements CheckoutConfigurationInterface
{
    private const TYPE_PICKUP = 'pickup';
    private const TYPE_DELIVERY = 'delivery';

    /** @var ShippingOptions */
    private $shippingOptions;

    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    public function getValue(): string
    {
        return $this->shippingOptions->isPakjegemakDefault() ? self::TYPE_PICKUP : self::TYPE_DELIVERY;
    }
}
