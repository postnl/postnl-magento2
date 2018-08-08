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
