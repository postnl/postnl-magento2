<?php

namespace TIG\PostNL\Config\Provider;

/**
 * @codingStandardsIgnoreFile
 */
class ReturnOptions extends AbstractConfigProvider
{
    const XPATH_RETURN_IS_ACTIVE            = 'tig_postnl/returns/returns_active';
    const XPATH_RETURN_LABEL                = 'tig_postnl/returns/label';
    const XPATH_RETURN_TO                   = 'tig_postnl/returns/returns_to';
    const XPATH_RETURN_LABEL_TYPE           = 'tig_postnl/returns/labels_type';
    const XPATH_RETURN_CITY                 = 'tig_postnl/returns/city';
    const XPATH_RETURN_COMPANY              = 'tig_postnl/returns/company';
    const XPATH_RETURN_STREETNAME           = 'tig_postnl/returns/streetname';
    const XPATH_RETURN_HOUSENUMBER          = 'tig_postnl/returns/housenumber';
    const XPATH_RETURN_HOUSENUMBER_EX       = 'tig_postnl/returns/housenumber_ex';
    const XPATH_RETURN_FREEPOST_NUMBER      = 'tig_postnl/returns/freepost_number';
    const XPATH_RETURN_ZIPCODE              = 'tig_postnl/returns/zipcode';
    const XPATH_RETURN_CUSTOMER_CODE        = 'tig_postnl/returns/customer_code';
    const XPATH_SMART_RETURN_IS_ACTIVE      = 'tig_postnl/returns/smart_returns_active';
    const XPATH_SMART_RETURN_EMAIL_TEMPLATE = 'tig_postnl/returns/smart_returns_template';

    /**
     * @return bool
     */
    public function isReturnActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_RETURN_IS_ACTIVE);
    }

    /**
     * @return bool
     */
    public function isSmartReturnActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_SMART_RETURN_IS_ACTIVE);
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
        return (int)$this->getConfigFromXpath(self::XPATH_RETURN_LABEL);
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
     * @return mixed
     */
    public function getStreetName()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_STREETNAME);
    }

    /**
     * @return mixed
     */
    public function getHouseNumber()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_HOUSENUMBER);
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

    /**
     * @return mixed
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
}
