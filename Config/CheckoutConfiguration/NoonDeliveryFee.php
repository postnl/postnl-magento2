<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Carrier\Price\TaxedFee;

class NoonDeliveryFee implements CheckoutConfigurationInterface
{
    private ShippingOptions $shippingOptions;
    private TaxedFee $taxedFee;

    public function __construct(ShippingOptions $shippingOptions, TaxedFee $taxedFee)
    {
        $this->shippingOptions = $shippingOptions;
        $this->taxedFee        = $taxedFee;
    }

    public function getValue()
    {
        return $this->taxedFee->get($this->shippingOptions->getNoonDeliveryFee());
    }
}
