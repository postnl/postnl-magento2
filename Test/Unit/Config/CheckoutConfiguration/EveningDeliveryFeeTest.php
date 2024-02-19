<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Config\CheckoutConfiguration\EveningDeliveryFee;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Test\TestCase;

class EveningDeliveryFeeTest extends TestCase
{
    public $instanceClass = EveningDeliveryFee::class;

    public function testGetValue()
    {
        $expected = 2;
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();

        $expects = $shippingOptions->expects($this->once());
        $expects->method('getEveningDeliveryFee');
        $expects->willReturn($expected);

        /** @var EveningDeliveryFee $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
