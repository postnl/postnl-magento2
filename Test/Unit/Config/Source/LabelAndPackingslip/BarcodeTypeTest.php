<?php

namespace TIG\PostNL\Unit\Config\Source\LabelAndPackingslip;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\LabelAndPackingslip\BarcodeType;

class BarcodeTypeTest extends TestCase
{
    protected $instanceClass = BarcodeType::class;

    public function testToOptionArray()
    {
        $expectedValues = ['I25', 'C39', 'C128', 'RMS4CC'];
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
