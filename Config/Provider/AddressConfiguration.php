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
     * @param null|int $store
     * Returns an array with all the address information of the shipper/merchant.
     * @return array
     */
    public function getAddressInfo($store = null)
    {
        $shippersAddress = [
            'firstname'            => $this->getFirstname($store),
            'lastname'             => $this->getLastname($store),
            'company'              => $this->getCompany($store),
            'street'               => $this->getStreetname($store),
            'department'           => $this->getDepartment($store),
            'housenumber'          => $this->getHousenumber($store),
            'housenumber_addition' => $this->getHousenumberAddition($store),
            'postcode'             => $this->getPostcode($store),
            'city'                 => $this->getCity($store)
        ];

        return $shippersAddress;
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Firstname
     * @return mixed
     */
    public function getFirstname($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_FIRSTNAME);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Lastname
     * @return mixed
     */
    public function getLastname($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_LASTNAME);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Companyname
     * @return mixed
     */
    public function getCompany($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERALS_COMPANY);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Streetname
     * @return mixed
     */
    public function getStreetname($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_STREETNAME);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Department
     * @return mixed
     */
    public function getDepartment($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_DEPARTMENT);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Housenumber
     * @return mixed
     */
    public function getHousenumber($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_HOUSENUMBER);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Addition to housenumber
     * @return mixed
     */
    public function getHousenumberAddition($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_HOUSENUMBER_ADDITION);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address Postcode
     * @return mixed
     */
    public function getPostcode($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_POSTCODE);
    }

    /**
     * @param null|int $store
     * Returns Shippers Address City
     * @return mixed
     */
    public function getCity($store = null)
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_CITY);
    }
}
