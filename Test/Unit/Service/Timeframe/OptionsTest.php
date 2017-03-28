<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Service\Timeframe;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Options;
use TIG\PostNL\Test\TestCase;

class OptionsTest extends TestCase
{
    public $instanceClass = Options::class;

    public function returnsTheRightOptionsProvider()
    {
        return [
            'evening and sunday disabled' => [false, false, ['Daytime']],
            'evening disabled, sunday enabled' => [false, true, ['Daytime', 'Sunday']],
            'evening and sunday enabled' => [true, true, ['Daytime', 'Evening', 'Sunday']],
            'evening enabled, sunday disabled' => [true, false, ['Daytime', 'Evening']],
        ];
    }

    /**
     * @dataProvider returnsTheRightOptionsProvider
     *
     * @param $eveningEnabled
     * @param $sundayEnabled
     * @param $expected
     */
    public function testReturnsTheRightOptions($eveningEnabled, $sundayEnabled, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class, true);
        $this->mockFunction($shippingOptions, 'isEveningDeliveryActive', $eveningEnabled);
        $this->mockFunction($shippingOptions, 'isSundayDeliveryActive', $sundayEnabled);

        /** @var Options $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
        ]);

        $this->assertEquals($expected, $instance->get());
    }
}
