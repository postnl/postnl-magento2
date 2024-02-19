<?php

namespace TIG\PostNL\Unit\Service\Timeframe\Filters\Options;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Timeframe\Filters\Options\Sunday;
use TIG\PostNL\Config\Provider\ShippingOptions;

class SundayTest extends TestCase
{
    protected $instanceClass = Sunday::class;

    /**
     * @dataProvider TIG\PostNL\Test\Fixtures\Timeframes\Options\DataProvider::sunday
     *
     * @param $isEnabled
     * @param $options
     * @param $expected
     */
    public function testFilter($isEnabled, $options, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $expects = $shippingOptions->expects($this->once());
        $expects->method('isSundayDeliveryActive');
        $expects->willReturn($isEnabled);

        $instance = $this->getInstance(['shippingOptions' => $shippingOptions]);
        $result   = $instance->filter($options);

        $this->assertEquals($expected, $result);
    }
}
