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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Validation;

use TIG\PostNL\Config\Provider\AddressConfiguration;

class CountryShipping
{
    /**
     * @var AddressConfiguration
     */
    private $addressConfiguration;

    /**
     * @param AddressConfiguration $addressConfiguration
     */
    public function __construct(AddressConfiguration $addressConfiguration)
    {
        $this->addressConfiguration = $addressConfiguration;
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function isShippingNLDomestic($country)
    {
        return ($country == 'NL' && $this->addressConfiguration->getCountry() == 'NL');
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function isShippingBEDomestic($country)
    {
        return ($country === 'BE' && $this->addressConfiguration->getCountry() == 'BE');
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function isShippingNLtoBE($country)
    {
        return ($country === 'BE' && $this->addressConfiguration->getCountry() == 'NL');
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function isShippingNLToEps($country)
    {
        return (!in_array($country, ['BE', 'NL']) && $this->addressConfiguration->getCountry() == 'NL');
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function isShippingBEToEps($country)
    {
        return ($country !== 'BE' && $this->addressConfiguration->getCountry() == 'BE');
    }
}
