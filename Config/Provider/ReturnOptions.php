<?php

namespace TIG\PostNL\Config\Provider;

use TIG\PostNL\Config\Source\General\Country;
use TIG\PostNL\Config\Source\Settings\ReturnTypes;

/**
 * @codingStandardsIgnoreFile
 */
class ReturnOptions extends AbstractConfigProvider
{
    const XPATH_RETURN_IS_ACTIVE            = 'tig_postnl/returns/returns_active';
    const XPATH_RETURN_LABEL                = 'tig_postnl/returns/label';
    const XPATH_RETURN_LABEL_BE             = 'tig_postnl/returns/label_be';
    const XPATH_RETURN_TO                   = 'tig_postnl/returns/returns_to';
    const XPATH_RETURN_LABEL_TYPE           = 'tig_postnl/returns/labels_type';
    const XPATH_RETURN_CITY                 = 'tig_postnl/returns/city';
    const XPATH_RETURN_COMPANY              = 'tig_postnl/returns/company';
    const XPATH_RETURN_STREETNAME           = 'tig_postnl/returns/streetname';
    const XPATH_RETURN_HOUSENUMBER          = 'tig_postnl/returns/housenumber';
    const XPATH_RETURN_HOUSENUMBER_EX       = 'tig_postnl/returns/housenumber_ex';
    const XPATH_RETURN_FREEPOST_NUMBER      = 'tig_postnl/returns/freepost_number';
    const XPATH_RETURN_ZIPCODE              = 'tig_postnl/returns/zipcode';
    const XPATH_RETURN_ZIPCODE_HOME         = 'tig_postnl/returns/zipcode_home';
    const XPATH_RETURN_CUSTOMER_CODE        = 'tig_postnl/returns/customer_code';
    const XPATH_SMART_RETURN_IS_ACTIVE      = 'tig_postnl/returns/smart_returns_active';
    const XPATH_SMART_RETURN_EMAIL_TEMPLATE = 'tig_postnl/returns/smart_returns_template';
    const XPATH_EASY_RETURN_SERVICE_ACTIVE  = 'tig_postnl/returns/easy_return_service_active';

    /**
     * @return bool
     */
    public function isReturnActive(): bool
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_RETURN_IS_ACTIVE);
    }

    public function isEasyReturnServiceActive(): bool
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_EASY_RETURN_SERVICE_ACTIVE);
    }

    /**
     * @return bool
     */
    public function isSmartReturnActive(): bool
    {
        return $this->isReturnActive()
            && (bool)$this->getConfigFromXpath(self::XPATH_SMART_RETURN_IS_ACTIVE)
            && $this->getGeneralCountry() === Country::COUNTRY_NL;
    }

    /**
     * @return int
     */
    public function getReturnTo(): int
    {
        return (int)$this->getConfigFromXpath(self::XPATH_RETURN_TO);
    }

    /**
     * @return int
     */
    public function getReturnLabel(): int
    {
        if ($this->getGeneralCountry() === Country::COUNTRY_NL) {
            return (int)$this->getConfigFromXpath(self::XPATH_RETURN_LABEL);
        }
        // BE has less options available right now, so uses separate configuration
        return (int)$this->getConfigFromXpath(self::XPATH_RETURN_LABEL_BE);
    }

    /**
     * @return int
     */
    public function getReturnLabelType(): int
    {
        return (int)$this->getConfigFromXpath(self::XPATH_RETURN_LABEL_TYPE);
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_CITY);
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_COMPANY);
    }

    /**
     * Fall back to Sender country
     * @return mixed
     */
    public function getCountry()
    {
        return $this->getConfigFromXpath(AddressConfiguration::XPATH_GENERAL_COUNTRY);
    }

    /**
     * @return mixed
     */
    public function getStreetName()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_STREETNAME);
    }

    /**
     * @return mixed
     */
    public function getHouseNumber(): string
    {
        return (string)$this->getConfigFromXpath(self::XPATH_RETURN_HOUSENUMBER);
    }

    /**
     * @return string
     */
    public function getHouseNumberEx(): string
    {
        return (string)$this->getConfigFromXpath(self::XPATH_RETURN_HOUSENUMBER_EX);
    }

    /**
     * @return mixed
     */
    public function getFreepostNumber()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_FREEPOST_NUMBER);
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_ZIPCODE);
    }

    public function getZipcodeHome(): string
    {
        $value = (string)$this->getConfigFromXpath(self::XPATH_RETURN_ZIPCODE_HOME);
        // Fallback to pre-configured value before this was introduced.
        if (!$value) {
            $value = (string)$this->getZipcode();
        }
        return $value;
    }

    public function getSelectedZipcode(): string
    {
        if ($this->getReturnTo() === ReturnTypes::TYPE_HOME_ADDRESS) {
            return $this->getZipcodeHome();
        }
        return $this->getZipcode();
    }

    /**
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_CUSTOMER_CODE);
    }

    /**
     * @return mixed
     */
    public function getEmailTemplate()
    {
        if(!$this->getConfigFromXpath(self::XPATH_SMART_RETURN_EMAIL_TEMPLATE)){
            return 'tig_postnl_postnl_settings_returns_template';
        }
        return $this->getConfigFromXpath(self::XPATH_SMART_RETURN_EMAIL_TEMPLATE);
    }

    public function getGeneralCountry(): string
    {
        return $this->getConfigFromXpath(AddressConfiguration::XPATH_GENERAL_COUNTRY);
    }
}
