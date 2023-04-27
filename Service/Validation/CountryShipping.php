<?php

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
