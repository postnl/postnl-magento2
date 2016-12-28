<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;

class DefaultConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = DefaultConfiguration::class;

    /**
     * @dataProvider randomWordsProvider
     */
    public function testGetWsdlBaseUrl($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_CIF_BASE_URL, $value);
        $this->assertEquals($value, $instance->getCifBaseUrl());
    }

    /**
     * @dataProvider randomWordsProvider
     */
    public function testGetTestWsdlBaseUrl($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_TEST_CIF_BASE_URL, $value);
        $this->assertEquals($value, $instance->getTestCifBaseUrl());
    }

    /**
     * @dataProvider liveStagingProvider
     */
    public function testGetModusWsdlBaseUrl($value, $modus)
    {
        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class)->getMock();

        $isModusLiveExpects = $accountConfigurationMock->expects($this->once());
        $isModusLiveExpects->method('isModusLive');
        $isModusLiveExpects->willReturn($value == 1);

        $instance = $this->getInstance(['accountConfiguration' => $accountConfigurationMock]);
        if ($modus == 'live') {
            $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_CIF_BASE_URL, 'live');
        } else {
            $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_TEST_CIF_BASE_URL, 'staging', null, $this->at(0));
        }

        $result = $instance->getModusCifBaseUrl();
        if ($modus == 'off') {
            $this->assertEquals('staging', $result);
        } else {
            $this->assertEquals($modus, $result);
        }
    }

    /**
     * @dataProvider randomWordsProvider
     */
    public function testGetApiBaseUrl($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_API_BASE_URL, $value);
        $this->assertEquals($value, $instance->getApiBaseUrl());
    }

    /**
     * @dataProvider randomWordsProvider
     */
    public function testGetTestApiBaseUrl($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_TEST_API_BASE_URL, $value);
        $this->assertEquals($value, $instance->getTestApiBaseUrl());
    }

    /**
     * @dataProvider randomWordsProvider
     */
    public function testGetBarcodeGlobalType($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_BARCODE_GLOBAL_TYPE, $value);
        $this->assertEquals($value, $instance->getBarcodeGlobalType());
    }

    /**
     * @dataProvider randomWordsProvider
     */
    public function testGetBarcodeGlobalRange($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_BARCODE_GLOBAL_RANGE, $value);
        $this->assertEquals($value, $instance->getBarcodeGlobalRange());
    }

    /**
     * @dataProvider liveStagingProvider
     */
    public function testGetModusApiBaseUrl($value, $modus)
    {
        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class)->getMock();

        $isModusLiveExpects = $accountConfigurationMock->expects($this->once());
        $isModusLiveExpects->method('isModusLive');
        $isModusLiveExpects->willReturn($value == 1);

        $instance = $this->getInstance(['accountConfiguration' => $accountConfigurationMock]);
        if ($modus == 'live') {
            $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_API_BASE_URL, 'live');
        } else {
            $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_TEST_API_BASE_URL, 'staging', null, $this->at(0));
        }

        $result = $instance->getModusApiBaseUrl();
        if ($modus == 'off') {
            $this->assertEquals('staging', $result);
        } else {
            $this->assertEquals($modus, $result);
        }
    }
}
