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
 * @codingStandardsIgnoreStart
 */
class ReturnOptions extends AbstractConfigProvider
{
    const XPATH_RETURN_IS_ACTIVE_NL = 'tig_postnl/returns_nl/returns_nl_active';
    const XPATH_RETURN_NL_CITY = 'tig_postnl/returns_nl/city';
    const XPATH_RETURN_NL_COMPANY = 'tig_postnl/returns_nl/company';
    const XPATH_RETURN_NL_HOUSENUMBER = 'tig_postnl/returns_nl/housenumber';
    const XPATH_RETURN_NL_FREEPOST_NUMBER = 'tig_postnl/returns_nl/freepost_number';
    const XPATH_RETURN_NL_ZIPCODE = 'tig_postnl/returns_nl/zipcode';

    const XPATH_RETURN_IS_ACTIVE_BE = 'tig_postnl/returns_be/returns_be_active';
    const XPATH_RETURN_BE_CITY = 'tig_postnl/returns_be/city';
    const XPATH_RETURN_BE_COMPANY = 'tig_postnl/returns_be/company';
    const XPATH_RETURN_BE_HOUSENUMBER = 'tig_postnl/returns_be/housenumber';
    const XPATH_RETURN_BE_FREEPOST_NUMBER = 'tig_postnl/returns_be/freepost_number';
    const XPATH_RETURN_BE_ZIPCODE = 'tig_postnl/returns_be/zipcode';

    /**
     * @return bool
     */
    public function isReturnNlActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_RETURN_IS_ACTIVE_NL);
    }

    /**
     * @return mixed
     */
    public function getCityNL()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_NL_CITY);
    }

    /**
     * @return mixed
     */
    public function getCompanyNL()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_NL_COMPANY);
    }

    /**
     * @return mixed
     */
    public function getHouseNumberNL()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_NL_HOUSENUMBER);
    }

    /**
     * @return mixed
     */
    public function getFreepostNumberNL()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_NL_FREEPOST_NUMBER);
    }

    /**
     * @return mixed
     */
    public function getZipcodeNL()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_NL_ZIPCODE);
    }

    /**
     * @return bool
     */
    public function isReturnBEActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_RETURN_IS_ACTIVE_BE);
    }

    /**
     * @return mixed
     */
    public function getCityBE()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_BE_CITY);
    }

    /**
     * @return mixed
     */
    public function getCompanyBE()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_BE_COMPANY);
    }

    /**
     * @return mixed
     */
    public function getHouseNumberBE()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_BE_HOUSENUMBER);
    }

    /**
     * @return mixed
     */
    public function getFreepostNumberBE()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_BE_FREEPOST_NUMBER);
    }

    /**
     * @return mixed
     */
    public function getZipcodeBE()
    {
        return $this->getConfigFromXpath(self::XPATH_RETURN_BE_ZIPCODE);
    }
}
