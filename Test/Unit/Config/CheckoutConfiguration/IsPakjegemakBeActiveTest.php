<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Config\CheckoutConfiguration\IsPakjegemakBeActive;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Test\TestCase;

class IsPakjegemakBeActiveTest extends TestCase
{
    public $instanceClass = IsPakjegemakBeActive::class;

    public function getValueProvider()
    {
        return [
            'is active' => [true],
            'is not active' => [true],
        ];
    }

    /**
     * @dataProvider getValueProvider
     *
     * @param $expected
     *
     * @throws \Exception
     */
    public function testGetValue($expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();

        $expects = $shippingOptions->expects($this->once());
        $expects->method('isPakjegemakActive')->with('BE');
        $expects->willReturn($expected);

        /** @var IsPakjegemakBeActive $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
