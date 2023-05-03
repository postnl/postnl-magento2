<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\CheckoutConfiguration\IsPostcodecheckActive;
use TIG\PostNL\Config\Provider\Webshop;

class IsPostcodecheckActiveTest extends TestCase
{
    protected $instanceClass = IsPostcodecheckActive::class;

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
        $webshopConfig = $this->getFakeMock(Webshop::class)->getMock();

        $expects = $webshopConfig->expects($this->once());
        $expects->method('getIsAddressCheckEnabled');
        $expects->willReturn($expected);

        /** @var IsPostcodecheckActive $instance */
        $instance = $this->getInstance([
            'webshop' => $webshopConfig,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
