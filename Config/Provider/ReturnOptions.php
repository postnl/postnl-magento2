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
namespace TIG\PostNL\Config\Provider;

/**
 * @codingStandardsIgnoreFile
 */
class ReturnOptions extends AbstractConfigProvider
{
    const XPATH_RETURN_IS_ACTIVE            = 'tig_postnl/returns/returns_active';
    const XPATH_RETURN_CITY                 = 'tig_postnl/returns/city';
    const XPATH_RETURN_COMPANY              = 'tig_postnl/returns/company';
    const XPATH_RETURN_STREETNAME           = 'tig_postnl/returns/streetname';
    const XPATH_RETURN_HOUSENUMBER          = 'tig_postnl/returns/housenumber';
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
