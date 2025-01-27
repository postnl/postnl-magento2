<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;

class PakjegemakGlobalCountries implements CheckoutConfigurationInterface
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

    public function getValue(): array
    {
        return $this->shippingOptions->getPakjegemakGlobalCountries();
    }
}
