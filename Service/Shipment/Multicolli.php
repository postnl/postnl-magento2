<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Config\Provider\AddressConfiguration;

class Multicolli
{
    /**
     * @var AddressConfiguration
     */
    private $addressConfiguration;

    /**
     * @param AddressConfiguration $addressConfiguration
     */
    public function __construct(
        AddressConfiguration $addressConfiguration
    ) {
        $this->addressConfiguration = $addressConfiguration;
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function get($country)
    {
        if ($this->addressConfiguration->getCountry() != 'NL') {
            return false;
        }

        return in_array($country, ['NL', 'BE']);
    }
}
