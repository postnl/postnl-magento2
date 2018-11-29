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
namespace TIG\PostNL\Unit\Test\Block\Adminhtml\Config\General;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Block\Adminhtml\Config\General\AccountSettings;
use TIG\PostNL\Config\Provider\AccountConfiguration;

class AccountSettingsTest extends TestCase
{
    protected $instanceClass = AccountSettings::class;

    /**
     * @return array
     */
    public function fieldProvider()
    {
        return [
            'inactive' => [
                '0',
                'modus_off'
            ],
            'live modus' => [
                '1',
                'modus_live'
            ],
            'test modus' => [
                '2',
                'modus_test'
            ]
        ];
    }

    /**
     * @dataProvider fieldProvider
     * @param $configValue
     * @param $expected
     */
    public function testGetModusClass($configValue, $expected)
    {
        $modusMock = $this->getFakeMock(AccountConfiguration::class, true);
        $modusMock->method('getModus')->willReturn($configValue);

        $instance = $this->getInstance([
            'accountConfiguration' => $modusMock
        ]);

        $this->assertEquals($expected, $instance->getModusClass());
    }

    public function testGetInfoUrlForDeliveryoptions()
    {
        $instance = $this->getInstance();
        $result = $instance->getInfoUrlForDeliveryoptions();

        $this->assertTrue(is_string($result));
    }

    public function testGetInfoUrlForTestAccount()
    {
        $instance = $this->getInstance();
        $result = $instance->getInfoUrlForTestAccount();

        $this->assertTrue(is_string($result));
    }
}
