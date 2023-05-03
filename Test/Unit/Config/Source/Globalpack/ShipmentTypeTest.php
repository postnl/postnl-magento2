<?php

namespace TIG\PostNL\Unit\Config\Source\Globalpack;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\Globalpack\ShipmentType;

class ShipmentTypeTest extends TestCase
{
    protected $instanceClass = ShipmentType::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertCount(5, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }
}
