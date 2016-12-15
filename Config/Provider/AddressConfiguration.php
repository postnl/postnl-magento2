<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\Provider;

/**
 * Class AddressConfiguration
 *
 * @package TIG\PostNL\Config\Provider
 */
class AddressConfiguration extends AbstractConfigProvider
{

    const XPATH_GENERAL_SHIPPINGADDRESS_FIRSTNAME            = 'tig_postnl/generalconfiguration_shipping_address/firstname';
    const XPATH_GENERAL_SHIPPINGADDRESS_LASTNAME             = 'tig_postnl/generalconfiguration_shipping_address/lastname';
    const XPATH_GENERAL_SHIPPINGADDRESS_COMPANY              = 'tig_postnl/generalconfiguration_shipping_address/company';
    const XPATH_GENERAL_SHIPPINGADDRESS_STREETNAME           = 'tig_postnl/generalconfiguration_shipping_address/streetname';
    const XPATH_GENERAL_SHIPPINGADDRESS_DEPARTMENT           = 'tig_postnl/generalconfiguration_shipping_address/department';
    const XPATH_GENERAL_SHIPPINGADDRESS_HOUSENUMBER          = 'tig_postnl/generalconfiguration_shipping_address/housenumber';
    const XPATH_GENERAL_SHIPPINGADDRESS_HOUSENUMBER_ADDITION = 'tig_postnl/generalconfiguration_shipping_address/housenumber_addition';
    const XPATH_GENERAL_SHIPPINGADDRESS_POSTCODE             = 'tig_postnl/generalconfiguration_shipping_address/postcode';
    const XPATH_GENERAL_SHIPPINGADDRESS_CITY                 = 'tig_postnl/generalconfiguration_shipping_address/city';

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
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_FIRSTNAME);
    }

    /**
     * Returns Shippers Address Lastname
     * @return mixed
     */
    public function getLastname()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_LASTNAME);
    }

    /**
     * Returns Shippers Address Companyname
     * @return mixed
     */
    public function getCompany()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_COMPANY);
    }

    /**
     * Returns Shippers Address Streetname
     * @return mixed
     */
    public function getStreetname()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_STREETNAME);
    }

    /**
     * Returns Shippers Address Department
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_DEPARTMENT);
    }

    /**
     * Returns Shippers Address Housenumber
     * @return mixed
     */
    public function getHousenumber()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_HOUSENUMBER);
    }

    /**
     * Returns Shippers Address Addition to housenumber
     * @return mixed
     */
    public function getHousenumberAddition()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_HOUSENUMBER_ADDITION);
    }

    /**
     * Returns Shippers Address Postcode
     * @return mixed
     */
    public function getPostcode()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_POSTCODE);
    }

    /**
     * Returns Shippers Address City
     * @return mixed
     */
    public function getCity()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_SHIPPINGADDRESS_CITY);
    }
}
