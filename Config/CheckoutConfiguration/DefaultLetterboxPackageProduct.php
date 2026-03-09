<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ProductOptions;

class DefaultLetterboxPackageProduct implements CheckoutConfigurationInterface
{
    private ProductOptions $productOptions;

    public function __construct(ProductOptions $productOptions)
    {
        $this->productOptions = $productOptions;
    }

    public function getValue(): string
    {
        return $this->productOptions->getDefaultLetterboxPackageProductSetting();
    }
}
