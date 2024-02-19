<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;

class IsPakjegemakActive implements CheckoutConfigurationInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param ShippingOptions      $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return (int) $this->shippingOptions->isPakjegemakActive();
    }
}
