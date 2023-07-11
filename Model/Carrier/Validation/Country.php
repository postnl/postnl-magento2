<?php

namespace TIG\PostNL\Model\Carrier\Validation;

use TIG\PostNL\Config\Provider\Globalpack;
use TIG\PostNL\Service\Shipment\EpsCountries;

class Country
{
    private $globalPackConfiguration;

    /**
     * Country constructor.
     *
     * @param Globalpack $globalpack
     */
    public function __construct(
        Globalpack $globalpack
    ) {
        $this->globalPackConfiguration = $globalpack;
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function validate($country)
    {
        if (!in_array($country, EpsCountries::ALL)) {
            return $this->globalPackConfiguration->isEnabled();
        }

        return true;
    }
}
