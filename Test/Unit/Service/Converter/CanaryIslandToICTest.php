<?php

namespace TIG\PostNL\Test\Unit\Service\Converter;

use TIG\PostNL\Service\Converter\CanaryIslandToIC;
use TIG\PostNL\Test\TestCase;
use Magento\Sales\Model\Order\Address;

class CanaryIslandToICTest extends TestCase
{
    public $instanceClass = CanaryIslandToIC::class;


    public function differentConvertCountriesProvider()
    {
        // Country, Postcode, Expected country
        return [
            'ES in canary' => ['ES', '35006', 'IC'],
            'ES not in canary ' => ['ES', '28013', 'ES'],
            'NL' => ['NL', '1014BA', 'NL'],
        ];
    }

    public function differentCheckCountriesProvider()
    {
        // Country, Postcode, Expected country
        return [
            'ES in canary' => ['ES', '35006', true],
            'ES not in canary ' => ['ES', '28013', false],
            'NL' => ['NL', '1014BA', false],
            'US' => ['US', '35006', false],
        ];
    }

    /**
     * @dataProvider differentConvertCountriesProvider
     *
     * @param $country
     * @param $postcode
     * @param $expected
     *
     * @throws \Exception
     */
    public function testConverter($country, $postcode, $expected)
    {
        $address = $this->getObject(Address::class);
        $address->setCountryId($country);
        $address->setPostcode($postcode);

        $this->getInstance()->convert($address);

        $this->assertEquals($expected, $address->getCountryId());
    }

    /**
     * @dataProvider differentCheckCountriesProvider
     *
     * @param $country
     * @param $postcode
     * @param $expected
     *
     * @throws \Exception
     */
    public function testIsCanaryIsland($country, $postcode, $expected)
    {
        $address = $this->getObject(Address::class);
        $address->setCountryId($country);
        $address->setPostcode($postcode);

        $isCanary = $this->getInstance()->isCanaryIsland($address);
        $this->assertEquals($expected, $isCanary);
    }
}
