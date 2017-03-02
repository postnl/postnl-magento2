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

class AddressConfiguration extends AbstractConfigProvider
{

    const XPATH_GENERAL_FIRSTNAME            = 'tig_postnl/generalconfiguration_shipping_address/firstname';
    const XPATH_GENERAL_LASTNAME             = 'tig_postnl/generalconfiguration_shipping_address/lastname';
    const XPATH_GENERALS_COMPANY             = 'tig_postnl/generalconfiguration_shipping_address/company';
    const XPATH_GENERAL_STREETNAME           = 'tig_postnl/generalconfiguration_shipping_address/streetname';
    const XPATH_GENERAL_DEPARTMENT           = 'tig_postnl/generalconfiguration_shipping_address/department';
    const XPATH_GENERAL_HOUSENUMBER          = 'tig_postnl/generalconfiguration_shipping_address/housenumber';
    const XPATH_GENERAL_HOUSENUMBER_ADDITION = 'tig_postnl/generalconfiguration_shipping_address/housenumber_addition';
    const XPATH_GENERAL_POSTCODE             = 'tig_postnl/generalconfiguration_shipping_address/postcode';
    const XPATH_GENERAL_CITY                 = 'tig_postnl/generalconfiguration_shipping_address/city';

    /**
     * Returns an array with all the address information of the shipper/merchant.
     * @return array
     */
    public function getAddressInfo()
    {
        $shippersAddress = [
            'firstname'            => $this->getFirstname(),
            'lastname'             => $this->getLastname(),
            'company'              => $this->getCompany(),
            'street'               => $this->getStreetname(),
            'department'           => $this->getDepartment(),
            'housenumber'          => $this->getHousenumber(),
            'housenumber_addition' => $this->getHousenumberAddition(),
            'postcode'             => $this->getPostcode(),
            'city'                 => $this->getCity()
        ];

        return $shippersAddress;
    }

    /**
     * Retruns Shippers Address Fristname
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_FIRSTNAME);
    }

    /**
     * Returns Shippers Address Lastname
     * @return mixed
     */
    public function getLastname()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_LASTNAME);
    }

    /**
     * Returns Shippers Address Companyname
     * @return mixed
     */
    public function getCompany()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERALS_COMPANY);
    }

    /**
     * Returns Shippers Address Streetname
     * @return mixed
     */
    public function getStreetname()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_STREETNAME);
    }

    /**
     * Returns Shippers Address Department
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_DEPARTMENT);
    }

    /**
     * Returns Shippers Address Housenumber
     * @return mixed
     */
    public function getHousenumber()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_HOUSENUMBER);
    }

    /**
     * Returns Shippers Address Addition to housenumber
     * @return mixed
     */
    public function getHousenumberAddition()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_HOUSENUMBER_ADDITION);
    }

    /**
     * Returns Shippers Address Postcode
     * @return mixed
     */
    public function getPostcode()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_POSTCODE);
    }

    /**
     * Returns Shippers Address City
     * @return mixed
     */
    public function getCity()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_CITY);
    }
}
