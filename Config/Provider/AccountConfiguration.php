<?php

namespace TIG\PostNL\Config\Provider;

class AccountConfiguration extends AbstractConfigProvider
{
    const API_KEY_SAME = 'Same';
    const XPATH_GENERAL_STATUS_MODUS = 'tig_postnl/generalconfiguration_extension_status/modus';

    /** Paths to the live settings */
    const XPATH_GENERAL_STATUS_CUSTOMERNUMBER = 'tig_postnl/generalconfiguration_extension_status/customer_number';
    const XPATH_GENERAL_STATUS_CUSTOMERCODE = 'tig_postnl/generalconfiguration_extension_status/customer_code';
    const XPATH_GENERAL_STATUS_APIKEY = 'tig_postnl/generalconfiguration_extension_status/api_key';
    const XPATH_GENERAL_STATUS_NEW_APIKEY = 'tig_postnl/generalconfiguration_extension_status/new_api_key';
    const XPATH_GENERAL_STATUS_BLSCODE = 'tig_postnl/generalconfiguration_extension_status/bls_code';
    /**
     * For the test account we will use the same xpaths, but "_test" will be attached to the end of the string.
     */

    /**
     * Returns an array with all the account information needed for CIF connection
     *
     * @param null|int $scopeId
     *
     * @return array
     */
    public function getAccountInfo($scopeId = null, $websiteScope = false)
    {
        if ($this->isModusOff($scopeId, $websiteScope)) {
            return [];
        }

        return [
            'customernumber' => $this->getCustomerNumber($scopeId, $websiteScope),
            'customercode' => $this->getCustomerCode($scopeId, $websiteScope),
            'api_key' => $this->getApiKey($scopeId, $websiteScope),
            'bls_code' => $this->getBlsCode($scopeId, $websiteScope)
        ];
    }

    /**
     * Returns the customerNumber for live or test.
     *
     * @param null|int $scopeId
     *
     * @return mixed
     */
    public function getCustomerNumber($scopeId = null, $websiteScope = false)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_CUSTOMERNUMBER, $scopeId, $websiteScope);

        if ($websiteScope) {
            return $this->getWebsiteConfigFromXpath($modusXpath, $scopeId);
        }

        return $this->getConfigFromXpath($modusXpath, $scopeId);
    }

    /**
     * Returns the customerCode for live or test.
     *
     * @param null|int $scopeId
     *
     * @return mixed
     */
    public function getCustomerCode($scopeId = null, $websiteScope = false)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_CUSTOMERCODE, $scopeId, $websiteScope);

        if ($websiteScope) {
            return $this->getWebsiteConfigFromXpath($modusXpath, $scopeId);
        }

        return $this->getConfigFromXpath($modusXpath, $scopeId);
    }

    /**
     * Returns the API Key which is decrypted automatically in the _afterload method
     * Magento\Config\Model\Config\Backend\Encrypted R:74
     *
     * @param null|int $scopeId
     *
     * @return string
     */
    public function getApiKey($scopeId = null, $websiteScope = false)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_NEW_APIKEY, $scopeId, $websiteScope);
        $modusOldXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_APIKEY, $scopeId, $websiteScope);

        if ($websiteScope) {
            $value = $this->getWebsiteConfigFromXpath($modusXpath, $scopeId);
            if (!$value) {
                $value = $this->getWebsiteConfigFromXpath($modusOldXpath, $scopeId);
            }
        } else {
            $value = $this->getConfigFromXpath($modusXpath, $scopeId);
            if (!$value) {
                $value = $this->getConfigFromXpath($modusOldXpath, $scopeId);
            }
        }

        return $this->crypt->decrypt($value);
    }

    public function getNewApiKeyHeader($scopeId = null, $websiteScope = false): string
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_NEW_APIKEY, $scopeId, $websiteScope);
        $modusOldXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_APIKEY, $scopeId, $websiteScope);

        if ($websiteScope) {
            $newValue = $this->getWebsiteConfigFromXpath($modusXpath, $scopeId);
            $oldValue = $this->getWebsiteConfigFromXpath($modusOldXpath, $scopeId);
        } else {
            $newValue = $this->getConfigFromXpath($modusXpath, $scopeId);
            $oldValue = $this->getConfigFromXpath($modusOldXpath, $scopeId);
        }
        $newValue = $this->crypt->decrypt($newValue);
        $oldValue = $this->crypt->decrypt($oldValue);

        if ($newValue === $oldValue) {
            return self::API_KEY_SAME;
        }

        if ($newValue) {
            return 'Yes';
        }
        if ($oldValue) {
            return 'No';
        }

        return '';
    }

    public function isNewApiKeyUsed(): bool
    {
        return $this->getNewApiKeyHeader() === 'Yes';
    }

    /**
     * @param null|int $store
     *
     * @return mixed
     */
    public function getBlsCode($scopeId = null, $websiteScope = false)
    {
        $modusXpath = $this->getModusXpath(self::XPATH_GENERAL_STATUS_BLSCODE, $scopeId, $websiteScope);

        if ($websiteScope) {
            return $this->getWebsiteConfigFromXpath($modusXpath, $scopeId);
        }

        return $this->getConfigFromXpath($modusXpath, $scopeId);
    }

    /**
     * @param null|int $scopeId
     * Should return on of these values
     *  '1' => live ||
     *  '2' => test ||
     *  '0' => off
     *
     * @return mixed
     */
    public function getModus($scopeId = null, $websiteScope = false)
    {
        if (!$this->isModuleOutputEnabled()) {
            return '0';
        }

        if ($websiteScope) {
            return $this->getWebsiteConfigFromXpath(self::XPATH_GENERAL_STATUS_MODUS, $scopeId);
        }

        return $this->getConfigFromXpath(self::XPATH_GENERAL_STATUS_MODUS, $scopeId);
    }

    /**
     * Checks if the extension is on status live
     *
     * @param null|int $scopeId
     *
     * @return bool
     */
    public function isModusLive($scopeId = null, $websiteScope = false)
    {
        return $this->getModus($scopeId, $websiteScope) == '1';
    }

    /**
     * Checks if the extension is on status test
     *
     * @param null|int $scopeId
     *
     * @return bool
     */
    public function isModusTest($scopeId = null, $websiteScope = false)
    {
        return $this->getModus($scopeId, $websiteScope) == '2';
    }

    /**
     * Checks if the extension is on status off.
     *
     * @param null|int $scopeId
     *
     * @return bool
     */
    public function isModusOff($scopeId = null, $websiteScope = false)
    {
        return $this->getModus($scopeId, $websiteScope) == '0' || $this->getModus() == false;
    }

    /**
     * @param $xpath
     * @param null|int $scopeId
     *
     * @return string
     */
    private function getModusXpath($xpath, $scopeId = null, $websiteScope = false)
    {
        if ($this->isModusTest($scopeId, $websiteScope)) {
            $xpath .= '_test';
        }

        return $xpath;
    }
}
