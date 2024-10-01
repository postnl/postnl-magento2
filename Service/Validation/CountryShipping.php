<?php

namespace TIG\PostNL\Service\Validation;

use TIG\PostNL\Config\Provider\AddressConfiguration;

class CountryShipping
{
    private AddressConfiguration $addressConfiguration;

    /**
     * @param AddressConfiguration $addressConfiguration
     */
    public function __construct(AddressConfiguration $addressConfiguration)
    {
        $this->addressConfiguration = $addressConfiguration;
    }

    /**
     * @param string $country
     *
     * @return bool
     */
    public function isShippingNLDomestic(string $country): bool
    {
        return ($country === 'NL' && $this->addressConfiguration->getCountry() === 'NL');
    }

    /**
     * @param string $country
     *
     * @return bool
     */
    public function isShippingBEDomestic(string $country): bool
    {
        return ($country === 'BE' && $this->addressConfiguration->getCountry() === 'BE');
    }

    /**
     * @param string $country
     *
     * @return bool
     */
    public function isShippingNLtoBE(string $country): bool
    {
        return ($country === 'BE' && $this->addressConfiguration->getCountry() === 'NL');
    }

    /**
     * @param string $country
     *
     * @return bool
     */
    public function isShippingBEtoNL(string $country): bool
    {
        return ($country === 'NL' && $this->addressConfiguration->getCountry() === 'BE');
    }

    /**
     * @param string $country
     *
     * @return bool
     */
    public function isShippingNLToEps(string $country): bool
    {
        return (!in_array($country, ['BE', 'NL'], true) && $this->addressConfiguration->getCountry() === 'NL');
    }

    /**
     * @param string $country
     *
     * @return bool
     */
    public function isShippingBEToEps(string $country): bool
    {
        return ($country !== 'BE' && $this->addressConfiguration->getCountry() === 'BE');
    }
}
