<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;

class EveningDeliveryFee implements CheckoutConfigurationInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @return bool|mixed
     */
    public function getValue()
    {
        return $this->shippingOptions->getEveningDeliveryFee();
    }
}
