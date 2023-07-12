<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\Settings\Checkouts;

class CheckoutsTest extends TestCase
{
    protected $instanceClass = Checkouts::class;

    private $compatibleCheckouts = [
        'default', 'blank', 'mageplaza', 'danslo', 'amasty'
    ];

    public function testToOptionArray()
    {
        $instance = $this->getInstance();

        $result = $instance->toOptionArray();

        $this->assertCount(5, $result);

        foreach ($result as $checkout) {
            $this->assertArrayHasKey('label', $checkout);
            $this->assertArrayHasKey('value', $checkout);

            $inArray = in_array($checkout['value'], $this->compatibleCheckouts);
            $this->assertTrue($inArray);
        }
    }
}
