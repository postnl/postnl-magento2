<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\Webshop;

class IsPostcodecheckActive implements CheckoutConfigurationInterface
{
    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * IsPostcodecheckActive constructor.
     *
     * @param Webshop $webshop
     */
    public function __construct(
        Webshop $webshop
    ) {
        $this->webshopConfig = $webshop;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return (bool) $this->webshopConfig->getIsAddressCheckEnabled();
    }
}
