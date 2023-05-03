<?php

namespace TIG\PostNL\Config\Validator;

use TIG\PostNL\Config\Provider\AddressConfiguration;

class ValidAddress
{
    /**
     * @var array
     */
    private $addressInfo;

    /**
     * @param AddressConfiguration $addressConfiguration
     */
    public function __construct(
        AddressConfiguration $addressConfiguration
    ) {
        $this->addressInfo = $addressConfiguration->getAddressInfo();
    }

    /**
     * @return bool
     */
    public function check()
    {
        if (!$this->hasValidName()) {
            return false;
        }

        if (!$this->hasValidAddress()) {
            return false;
        }

        return true;
    }

    /**
     * There should be a valid first and lastname, or a valid company field.
     *
     * @return bool
     */
    public function hasValidName()
    {
        $validName = !empty($this->addressInfo['firstname']) && !empty($this->addressInfo['lastname']);
        $validCompany = !empty($this->addressInfo['company']);

        if ($validName || $validCompany) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function hasValidAddress()
    {
        if (empty($this->addressInfo['street'])) {
            return false;
        }

        if (empty($this->addressInfo['housenumber'])) {
            return false;
        }

        if (empty($this->addressInfo['postcode'])) {
            return false;
        }

        if (empty($this->addressInfo['city'])) {
            return false;
        }

        return true;
    }
}
