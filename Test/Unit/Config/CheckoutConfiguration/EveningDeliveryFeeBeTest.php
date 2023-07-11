<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Config\CheckoutConfiguration\EveningDeliveryBeFee;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Test\TestCase;

class EveningDeliveryFeeBeTest extends TestCase
{
    public $instanceClass = EveningDeliveryBeFee::class;

    public function testGetValue()
    {
        $expected = 2;
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();

        $expects = $shippingOptions->expects($this->once());
        $expects->method('getEveningDeliveryFee')->with('BE');
        $expects->willReturn($expected);

        /** @var EveningDeliveryBeFee $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
