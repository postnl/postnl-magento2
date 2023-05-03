<?php

namespace TIG\PostNL\Service\Validation;

class Region implements ContractInterface
{
    /**
     * @var Country
     */
    private $countryValidator;

    /**
     * @param Country $countryValidator
     */
    public function __construct(
        Country $countryValidator
    ) {
        $this->countryValidator = $countryValidator;
    }

    /**
     * Validate the data. Returns false when the
     *
     * @param array $line
     *
     * @return bool|mixed
     */
    public function validate($line)
    {
        $country = $line['country'];
        $regionName = $line['region'];
        if ($regionName == '*') {
            return 0;
        }

        $countries = $this->countryValidator->getCountryList();
        if (!array_key_exists($country, $countries)) {
            return false;
        }

        if (!array_key_exists($regionName, $countries[$country]['regions'])) {
            return false;
        }

        return $countries[$country]['regions'][$regionName];
    }
}
