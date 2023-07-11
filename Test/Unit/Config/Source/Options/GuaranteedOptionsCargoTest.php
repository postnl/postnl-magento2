<?php

namespace TIG\PostNL\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\GuaranteedOptionsCargo;
use TIG\PostNL\Test\TestCase;

class GuaranteedOptionsCargoTest extends TestCase
{
    protected $instanceClass = GuaranteedOptionsCargo::class;

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
}
