<?php

namespace TIG\PostNL\Unit\Config\Source\General;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\General\Country;

class CountryTest extends TestCase
{
    protected $instanceClass = Country::class;

    public function testToOptionArray()
    {
        $expectedValues = ['NL', 'BE'];
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
