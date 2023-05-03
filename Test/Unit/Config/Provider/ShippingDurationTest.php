<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\ShippingDuration;
use TIG\PostNL\Test\TestCase;

class ShippingDurationTest extends TestCase
{
    protected $instanceClass = ShippingDuration::class;

    public function testGetAllOptions()
    {
        $options = $this->getInstance()->getAllOptions();
        $defaultOptions = array_filter($options, function($item) {
            return $item['value'] === ShippingDuration::CONFIGURATION_VALUE;
        });

        $this->assertCount(1, $defaultOptions);
        $this->assertCount(16, $options);
    }
}
