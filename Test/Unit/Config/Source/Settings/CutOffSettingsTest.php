<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Config\Source\Settings\CutOffSettings;
use TIG\PostNL\Test\TestCase;

class CutOffSettingsTest extends TestCase
{
    public $instanceClass = CutOffSettings::class;

    public function testToOptionArray()
    {
        /** @var CutOffSettings $instance */
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertEquals('00:00', $result[0]['label']->render());

        // 24 hours x 4 = 96;
        $this->assertCount(96, $result);
        $this->assertEquals('00:00:00', $result[0]['value']);
        $this->assertEquals('00:15:00', $result[1]['value']);
        $this->assertEquals('00:30:00', $result[2]['value']);
        $this->assertEquals('00:45:00', $result[3]['value']);
        $this->assertEquals('23:45:00', end($result)['value']);
    }
}
