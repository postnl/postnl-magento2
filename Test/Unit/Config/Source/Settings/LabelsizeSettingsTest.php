<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\Settings\LabelsizeSettings;

class LabelsizeSettingsTest extends TestCase
{
    protected $instanceClass = LabelsizeSettings::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertCount(2, $result);

        foreach ($result as $labelType) {
            $this->assertArrayHasKey('label', $labelType);
            $this->assertArrayHasKey('value', $labelType);

            $inArray = in_array($labelType['value'], ['A4', 'A6']);
            $this->assertTrue($inArray);
        }
    }
}
