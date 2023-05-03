<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Config\CheckoutConfiguration\IsPakjegemakActive;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Test\TestCase;

class IsPakjegemakActiveTest extends TestCase
{
    public $instanceClass = IsPakjegemakActive::class;

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
     */
    public function testGetValue($expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();

        $expects = $shippingOptions->expects($this->once());
        $expects->method('isPakjegemakActive');
        $expects->willReturn($expected);

        /** @var IsPakjegemakActive $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
