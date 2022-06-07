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
