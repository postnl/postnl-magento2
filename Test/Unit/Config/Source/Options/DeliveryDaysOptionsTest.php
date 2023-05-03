<?php

namespace TIG\PostNL\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\DeliverydaysOptions;
use TIG\PostNL\Test\TestCase;

class DeliveryDaysOptionsTest extends TestCase
{
    protected $instanceClass = DeliverydaysOptions::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertCount(14, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }
}
