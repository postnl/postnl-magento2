<?php

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
