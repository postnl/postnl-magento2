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

class AccountConfiguration extends AbstractConfigProvider
{
    const XPATH_GENERAL_STATUS_MODUS = 'tig_postnl/generalconfiguration_extension_status/modus';

    /** Paths to the live settings */
    const XPATH_GENERAL_STATUS_CUSTOMERNUMBER = 'tig_postnl/generalconfiguration_extension_status/customer_number';
    const XPATH_GENERAL_STATUS_CUSTOMERCODE   = 'tig_postnl/generalconfiguration_extension_status/customer_code';
    const XPATH_GENERAL_STATUS_APIKEY         = 'tig_postnl/generalconfiguration_extension_status/api_key';
    const XPATH_GENERAL_STATUS_BLSCODE        = 'tig_postnl/generalconfiguration_extension_status/bls_code';
    /**
     * For the test account we will use the same xpaths, but "_test" will be attached to the end of the string.
     */

    /**
     * Returns an array with all the account information needed for CIF connection
     * @param null|int $store
     * @return array
     */
    public function getAccountInfo($store = null)
    {
        if ($this->isModusOff($store)) {
            return [];
        }

        $accountInfo = [
            'customernumber' => $this->getCustomerNumber($store),
            'customercode'   => $this->getCustomerCode($store),
            'api_key'        => $this->getApiKey($store),
            'bls_code'       => $this->getBlsCode($store)
        ];

        return $accountInfo;
    }

    /**
     * Returns the customerNumber for live or test.
     * @param null|int $store
     * @return mixed
     */
    public function getCustomerNumber($store = null)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_CUSTOMERNUMBER, $store);

        return $this->getConfigFromXpath($modusXpath, $store);
    }

    /**
     * Returns the customerCode for live or test.
     * @param null|int $store
     * @return mixed
     */
    public function getCustomerCode($store = null)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_CUSTOMERCODE, $store);

        return $this->getConfigFromXpath($modusXpath, $store);
    }

    /**
     * Returns the API Key which is decrypted automaticly in the _afterload method
     * Magento\Config\Model\Config\Backend\Encrypted R:74
     * @param null|int $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_APIKEY, $store);
        $value = $this->getConfigFromXpath($modusXpath, $store);

        return $this->crypt->decrypt($value);
    }

    /**
     * @param null|int $store
     * @return mixed
     */
    public function getBlsCode($store = null)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_BLSCODE, $store);

        return $this->getConfigFromXpath($modusXpath, $store);
    }

    /**
     * @param null|int $store
     * Should return on of these values
     *  '1' => live ||
     *  '2' => test ||
     *  '0' => off
     *
     * @return mixed
     */
    public function getModus($store = null)
    {
        if (!$this->isModuleOutputEnabled()) {
            return '0';
        }

        return $this->getConfigFromXpath(self::XPATH_GENERAL_STATUS_MODUS, $store);
    }

    /**
     * Checks if the extension is on status live
     * @param null|int $store
     * @return bool
     */
    public function isModusLive($store = null)
    {
        if ($this->getModus($store) == '1') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status test
     * @param null|int $store
     * @return bool
     */
    public function isModusTest($store = null)
    {
        if ($this->getModus($store) == '2') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status off.
     * @param null|int $store
     * @return bool
     */
    public function isModusOff($store = null)
    {
        if ($this->getModus($store) == '0' || false == $this->getModus()) {
            return true;
        }

        return false;
    }

    /**
     * @param $xpath
     * @param null|int $store
     *
     * @return string
     */
    private function getModusXpath($xpath, $store = null)
    {
        if ($this->isModusTest($store)) {
            $xpath .= '_test';
        }

        return $xpath;
    }
}
