<?php
declare(strict_types=1);

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;

class IsInternationalPacketsActive implements CheckoutConfigurationInterface
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
     * @return bool
     */
    public function getValue(): bool
    {
        return (bool) $this->shippingOptions->canUsePriority();
    }
}
