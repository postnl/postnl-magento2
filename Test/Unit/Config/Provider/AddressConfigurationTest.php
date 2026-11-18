<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Source\General\Country;

class AddressConfigurationTest extends AbstractConfiguration
{
    public $instanceClass = AddressConfiguration::class;

    /**
     * @return array
     */
    public static function countryProvider()
    {
        return [
            'NL' => [Country::COUNTRY_NL],
            'BE' => [Country::COUNTRY_BE]
        ];
    }

    /**
     * @dataProvider countryProvider
     *
     * @param $country
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('countryProvider')]
    public function testShouldReturnCountry($country)
    {
        $instance = $this->getInstance();
        $this->setXpath(AddressConfiguration::XPATH_GENERAL_COUNTRY, $country);

        $this->assertEquals($country, $instance->getCountry());
    }

    public function testGetAddressInfoContainsCountry()
    {
        $instance = $this->getInstance();
        $this->scopeConfigMock->expects($this->atLeastOnce())->method('getValue');

        $this->assertArrayHasKey('country', $instance->getAddressInfo());
    }
}
