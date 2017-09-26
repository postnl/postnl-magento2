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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;

class DefaultConfiguration extends AbstractConfigProvider
{
    const XPATH_ENDPOINTS_API_BASE_URL = 'tig_postnl/endpoints/api_base_url';
    const XPATH_ENDPOINTS_TEST_API_BASE_URL = 'tig_postnl/endpoints/test_api_base_url';

    const XPATH_BARCODE_GLOBAL_TYPE  = 'postnl/barcode/global_type';
    const XPATH_BARCODE_GLOBAL_RANGE = 'postnl/barcode/global_range';

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Encryptor $crypt
     * @param AccountConfiguration $accountConfiguration
     * @param Manager $moduleManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Encryptor $crypt,
        AccountConfiguration $accountConfiguration,
        Manager $moduleManager
    ) {
        parent::__construct($scopeConfig, $moduleManager, $crypt);
        $this->accountConfiguration = $accountConfiguration;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->getConfigFromXpath(static::XPATH_ENDPOINTS_API_BASE_URL);
    }

    /**
     * @return string
     */
    public function getTestApiBaseUrl()
    {
        return $this->getConfigFromXpath(static::XPATH_ENDPOINTS_TEST_API_BASE_URL);
    }

    /**
     * @return string
     */
    public function getModusApiBaseUrl()
    {
        if ($this->accountConfiguration->isModusLive()) {
            return $this->getApiBaseUrl();
        }

        return $this->getTestApiBaseUrl();
    }

    /**
     * @return string
     */
    public function getBarcodeGlobalType()
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_GLOBAL_TYPE);
    }

    /**
     * @return string
     */
    public function getBarcodeGlobalRange()
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_GLOBAL_RANGE);
    }
}
