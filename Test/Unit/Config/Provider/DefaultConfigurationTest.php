<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Test\Fixtures\DataProvider;

class DefaultConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = DefaultConfiguration::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testGetApiBaseUrl($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_API_BASE_URL, $value);
        $this->assertEquals($value, $instance->getApiBaseUrl());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testGetTestApiBaseUrl($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_TEST_API_BASE_URL, $value);
        $this->assertEquals($value, $instance->getTestApiBaseUrl());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testGetBarcodeGlobalType($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_BARCODE_GLOBAL_TYPE, $value);
        $this->assertEquals($value, $instance->getBarcodeGlobalType());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testGetBarcodeGlobalRange($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(DefaultConfiguration::XPATH_BARCODE_GLOBAL_RANGE, $value);
        $this->assertEquals($value, $instance->getBarcodeGlobalRange());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::liveStagingProvider
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
            $this->setXpath(DefaultConfiguration::XPATH_ENDPOINTS_TEST_API_BASE_URL, 'staging');
        }

        $result = $instance->getModusApiBaseUrl();
        if ($modus == 'off') {
            $this->assertEquals('staging', $result);
        } else {
            $this->assertEquals($modus, $result);
        }
    }
}
