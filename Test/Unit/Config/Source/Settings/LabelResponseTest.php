<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\Settings\LabelResponse;

class LabelResponseTest extends TestCase
{
    protected $instanceClass = LabelResponse::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertCount(2, $result);

        foreach ($result as $responseType) {
            $this->assertArrayHasKey('label', $responseType);
            $this->assertArrayHasKey('value', $responseType);

            $inArray = in_array($responseType['value'], ['attachment', 'inline']);
            $this->assertTrue($inArray);
        }
    }
}
