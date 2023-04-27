<?php

namespace TIG\PostNL\Unit\Config\Source\Globalpack;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\Globalpack\Direction;

class DirectionTest extends TestCase
{
    protected $instanceClass = Direction::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertCount(2, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }
}
