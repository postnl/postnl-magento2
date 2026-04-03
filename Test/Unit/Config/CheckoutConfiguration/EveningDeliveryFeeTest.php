<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Config\CheckoutConfiguration\EveningDeliveryFee;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Carrier\Price\TaxedFee;
use TIG\PostNL\Test\TestCase;

class EveningDeliveryFeeTest extends TestCase
{
    public $instanceClass = EveningDeliveryFee::class;

    public function testGetValue()
    {
        $fee = 2.0;
        $expected = 2.0;

        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $shippingOptions->expects($this->once())
            ->method('getEveningDeliveryFee')
            ->willReturn($fee);

        $taxedFee = $this->getFakeMock(TaxedFee::class)->getMock();
        $taxedFee->expects($this->once())
            ->method('get')
            ->with((float) $fee)
            ->willReturn($expected);

        /** @var EveningDeliveryFee $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
            'taxedFee'        => $taxedFee,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }
}
