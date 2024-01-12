<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Config\CheckoutConfiguration\SundayDeliveryFee;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Test\TestCase;

class SundayDeliveryFeeTest extends TestCase
{
    public $instanceClass = SundayDeliveryFee::class;

    public function testGetValue()
    {
        $expected = 2;
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();

        $expects = $shippingOptions->expects($this->once());
        $expects->method('getSundayDeliveryFee');
        $expects->willReturn($expected);

        /** @var SundayDeliveryFee $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
