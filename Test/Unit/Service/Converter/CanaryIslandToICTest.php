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
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($country);
        $address->setPostcode($postcode);

        $address = $this->getInstance()->convert($address);
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
