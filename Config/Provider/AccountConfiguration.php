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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\Provider;

/**
 * Class AccountConfiguration
 *
 * @package TIG\PostNL\Config\Provider
 */
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
     * @return array
     */
    public function getAccountInfo()
    {
        if ($this->isModusOff()) {
            return [];
        }

        $accountInfo = [
            'customernumber' => $this->getCustomerNumber(),
            'customercode'   => $this->getCustomerCode(),
            'api_key'        => $this->getApiKey(),
            'bls_code'       => $this->getBlsCode()
        ];

        return $accountInfo;
    }

    /**
     * Returns the customerNumber for live or test.
     * @return mixed
     */
    public function getCustomerNumber()
    {
        return $this->getConfigFromXpath($this->getModusXpath(self::XPATH_GENERAL_STATUS_CUSTOMERNUMBER));
    }

    /**
     * Returns the customerCode for live or test.
     * @return mixed
     */
    public function getCustomerCode()
    {
        return $this->getConfigFromXpath($this->getModusXpath(self::XPATH_GENERAL_STATUS_CUSTOMERCODE));
    }

    /**
     * Returns the API Key which is decrypted automaticly in the _afterload method
     * Magento\Config\Model\Config\Backend\Encrypted R:74
     * @return string
     */
    public function getApiKey()
    {
        $value = $this->getConfigFromXpath($this->getModusXpath(self::XPATH_GENERAL_STATUS_APIKEY));

        return $this->crypt->decrypt($value);
    }

    /**
     * @return mixed
     */
    public function getBlsCode()
    {
        return $this->getConfigFromXpath($this->getModusXpath(self::XPATH_GENERAL_STATUS_BLSCODE));
    }

    /**
     * Should return on of these values
     *  '1' => live ||
     *  '2' => test ||
     *  '0' => off
     *
     * @return mixed
     */
    public function getModus()
    {
        return $this->getConfigFromXpath(self::XPATH_GENERAL_STATUS_MODUS);
    }

    /**
     * Checks if the extension is on status live
     * @return bool
     */
    public function isModusLive()
    {
        if ($this->getModus() == '1') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status test
     * @return bool
     */
    public function isModusTest()
    {
        if ($this->getModus() == '2') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status off.
     * @return bool
     */
    public function isModusOff()
    {
        if ($this->getModus() == '0' || false == $this->getModus()) {
            return true;
        }

        return false;
    }

    /**
     * @param $xpath
     *
     * @return string
     */
    private function getModusXpath($xpath)
    {
        if ($this->isModusTest()) {
            $xpath .= '_test';
        }

        return $xpath;
    }
}
