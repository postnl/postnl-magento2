<?php

namespace TIG\PostNL\Test\Integration\Service\Validation;

use TIG\PostNL\Test\Integration\TestCase;

class RegionTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Service\Validation\Region::class;

    public function testInvalidRegion()
    {
        $instance = $this->getInstance();

        $this->assertFalse($instance->validate(['country' => 'NL', 'region' => 'non existing province']));
    }

    public function testValidRegion()
    {
        $instance = $this->getInstance();

        $this->assertEquals(12, $instance->validate(['country' => 'US', 'region' => 'CA']));
        $this->assertEquals(12, $instance->validate(['country' => 'US', 'region' => 'California']));
    }
}
