<?php

namespace TIG\PostNL\Unit\Config\Source\LabelAndPackingslip;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ShowShippingLabel;

class ShowShippingLabelTest extends TestCase
{
    protected $instanceClass = ShowShippingLabel::class;

    public function testToOptionArray()
    {
        $expectedValues = ['together', 'separate', 'none'];

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
