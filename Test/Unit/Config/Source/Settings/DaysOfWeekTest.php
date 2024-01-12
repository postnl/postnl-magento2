<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Config\Source\Settings\DaysOfWeek;
use TIG\PostNL\Test\TestCase;

class DaysOfWeekTest extends TestCase
{
    public $instanceClass = DaysOfWeek::class;

    public function testToOptionArray()
    {
        /** @var DaysOfWeek $instance */
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertCount(7, $result);
        $this->assertEquals('1', $result[0]['value']);
        $this->assertEquals('Monday', $result[0]['label']->render());
        $this->assertEquals('0', $result[6]['value']);
        $this->assertEquals('Sunday', $result[6]['label']->render());
    }
}
