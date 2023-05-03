<?php

namespace TIG\PostNL\Unit\Config\Source\LabelAndPackingslip;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ReferenceType;

class ReferenceTypeTest extends TestCase
{
    protected $instanceClass = ReferenceType::class;

    public function testToOptionArray()
    {
        $expectedValues = ['none', 'shipment_increment_id', 'order_increment_id', 'custom'];

        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertCount(count($expectedValues), $result);

        foreach ($result as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertContains($option['value'], $expectedValues);
        }
    }
}
