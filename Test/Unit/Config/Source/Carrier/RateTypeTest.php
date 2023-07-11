<?php

namespace TIG\PostNL\Unit\Config\Source\Carrier;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\Carrier\RateType;

class RateTypeTest extends TestCase
{
    protected $instanceClass = RateType::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertCount(3, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }

    public function testHasMatrixRate()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $hasOption = false;
        foreach ($options as $option) {
            if ($option['value'] == $instance::CARRIER_RATE_TYPE_MATRIX) {
                $hasOption = true;
            }
        }

        $this->assertTrue($hasOption);
    }
}
