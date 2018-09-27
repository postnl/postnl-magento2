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
namespace TIG\PostNL\Test\Unit\Plugin\Postcodecheck\Mageplaza;

use TIG\PostNL\Plugin\Postcodecheck\Mageplaza\AddressHelper;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\Webshop;

class AddressHelperTest extends TestCase
{
    protected $instanceClass = AddressHelper::class;

    public function pluginProvider()
    {
        return [
            'modus off is false' => [
                [], false, ['postcode-field-group' => [
                    'sortOrder' => 65,
                    'colspan'   => 12,
                    'isNewRow'  => true
                ]]
            ],
            'modus off is true' => [
                ['modus_off' => true], true, ['modus_off' => true]
            ]
        ];
    }

    /**
     * @param $fields
     * @param $moduleOff
     * @param $expected
     *
     * @dataProvider pluginProvider
     */
    public function testAfterGetAddressFieldPosition($fields, $moduleOff, $expected)
    {
        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class)->getMock();
        $expectedConfig = $accountConfigurationMock->expects($this->once());
        $expectedConfig->method('isModusOff');
        $expectedConfig->willReturn($moduleOff);

        $webshopConfigMock = $this->getFakeMock(Webshop::class)->getMock();
        $expectedWebshopConfig = $webshopConfigMock->expects($this->any());
        $expectedWebshopConfig->method('getIsAddressCheckEnabled');
        $expectedWebshopConfig->willReturn(true);

        $instance = $this->getInstance([
            'accountConfiguration' => $accountConfigurationMock,
            'webshop' => $webshopConfigMock
        ]);

        $result = $instance->afterGetAddressFieldPosition(null, $fields);
        $this->assertEquals($expected, $result);
    }
}
