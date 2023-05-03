<?php

namespace TIG\PostNL\Unit\Service\Timeframe\Filters\Options;

use Exception;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Timeframe\Filters\Options\Evening;
use TIG\PostNL\Config\Provider\ShippingOptions;

class EveningTest extends TestCase
{
    protected $instanceClass = Evening::class;

    /**
     * @dataProvider TIG\PostNL\Test\Fixtures\Timeframes\Options\DataProvider::evening
     *
     * @param $isEnabled
     * @param $options
     * @param $expected
     *
     * @throws Exception
     */
    public function testFilter($isEnabled, $options, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $expects = $shippingOptions->expects($this->once());
        $expects->method('isEveningDeliveryActive');
        $expects->willReturn($isEnabled);

        $instance = $this->getInstance(['shippingOptions' => $shippingOptions]);
        $result   = $instance->filter($options);

        $this->assertEquals($expected, $result);
    }
}
