<?php

namespace TIG\PostNL\Config\Source\Settings;

use \Magento\Framework\Option\ArrayInterface;
use TIG\PostNL\Config\Provider\ShippingDuration as SourceProvider;

class ShippingDuration implements ArrayInterface
{
    /**
     * @var SourceProvider
     */
    private $sourceProvider;

    /**
     * ShippingDuration constructor.
     *
     * @param SourceProvider $shippingDuration
     */
    public function __construct(
        SourceProvider $shippingDuration
    ) {
        $this->sourceProvider = $shippingDuration;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->sourceProvider->getAllOptions();
        $options = array_filter($options, function ($option) {
            return $option['value'] !== SourceProvider::CONFIGURATION_VALUE;
        });

        return $options;
    }
}
