<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

interface CheckoutConfigurationInterface
{
    /**
     * @return bool|string
     */
    public function getValue();
}
