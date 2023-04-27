<?php

namespace TIG\PostNL\Unit\Config\Source\LabelAndPackingslip;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\LabelAndPackingslip\BarcodeValue;

class BarcodeValueTest extends TestCase
{
    protected $instanceClass = BarcodeValue::class;

    public function testToOptionArray()
    {
        $expectedValues = ['shipment_increment_id', 'order_increment_id'];
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
