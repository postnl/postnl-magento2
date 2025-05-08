<?php

namespace TIG\PostNL\Test\Unit\Service\Timeframe;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Timeframe\Options;
use TIG\PostNL\Test\TestCase;

class OptionsTest extends TestCase
{
    public $instanceClass = Options::class;

    public function returnsTheRightOptionsProvider()
    {
        return [
            'evening and noon disabled' => [false, false, ['Daytime']],
            'evening disabled, noon enabled' => [false, true, ['Daytime', 'Noon']],
            'evening and noon enabled' => [true, true, ['Daytime', 'Evening', 'Noon']],
            'evening enabled, noon disabled' => [true, false, ['Daytime', 'Evening']],
        ];
    }

    /**
     * @dataProvider returnsTheRightOptionsProvider
     *
     * @param $eveningEnabled
     * @param $sundayEnabled
     * @param $expected
     */
    public function testReturnsTheRightOptions($eveningEnabled, $noonEnabled, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class, true);
        $this->mockFunction($shippingOptions, 'isEveningDeliveryActive', $eveningEnabled);
        $this->mockFunction($shippingOptions, 'isGuaranteedDeliveryActive', $noonEnabled);

        $webshopSettings = $this->getFakeMock(Webshop::class, true);
        $this->mockFunction($webshopSettings, 'getShipmentDays', '0,6');

        /** @var Options $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
            'webshop'         => $webshopSettings
        ]);

        $this->assertEquals($expected, $instance->get());
    }
}
