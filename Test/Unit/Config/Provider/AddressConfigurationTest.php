<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Source\General\Country;

class AddressConfigurationTest extends AbstractConfigurationTest
{
    public $instanceClass = AddressConfiguration::class;

    /**
     * @return array
     */
    public function countryProvider()
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
    public function testShouldReturnCountry($country)
    {
        $instance = $this->getInstance();
        $this->setXpath(AddressConfiguration::XPATH_GENERAL_COUNTRY, $country);

        $this->assertEquals($country, $instance->getCountry());
    }

    public function testGetAddressInfoContainsCountry()
    {
        $this->assertArrayHasKey('country', $this->getInstance()->getAddressInfo());
    }
}
