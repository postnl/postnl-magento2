<?php

namespace TIG\PostNL\Unit\Config\Source\LabelAndPackingslip;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\LabelAndPackingslip\BarcodeType;

class BarcodeTypeTest extends TestCase
{
    protected $instanceClass = BarcodeType::class;

    public function testToOptionArray()
    {
        $expectedValues = ['code25', 'code39', 'code128', 'royalmail'];
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