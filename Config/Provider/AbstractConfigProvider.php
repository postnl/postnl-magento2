<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;
use TIG\PostNL\Config\Source\Options\ProductOptions;

abstract class AbstractConfigProvider
{
    /**
     * @var ScopeConfigInterface
     */
    // @codingStandardsIgnoreLine
    protected $scopeConfig;

    /**
     * @var Manager $moduleManager
     */
    // @codingStandardsIgnoreLine
    protected $moduleManager;

    /**
     * @var Encryptor
     */
    // @codingStandardsIgnoreLine
    protected $crypt;

    /**
     * @var ProductOptions
     */
    // @codingStandardsIgnoreLine
    protected $productOptions;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager $moduleManager
     * @param Encryptor            $crypt
     * @param ProductOptions $productOptions
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        ProductOptions $productOptions
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->crypt = $crypt;
        $this->productOptions = $productOptions;
    }

    /**
     * Get Config value with xpath
     *
     * @param      $xpath
     * @param null $store
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    protected function getConfigFromXpath($xpath, $store = null)
    {
        return $this->scopeConfig->getValue(
            $xpath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Config value with xpath
     *
     * @param      $xpath
     * @param null $website
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    protected function getWebsiteConfigFromXpath($xpath, $website = null)
    {
        return $this->scopeConfig->getValue(
            $xpath,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }

    /**
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function isModuleOutputEnabled()
    {
        return $this->moduleManager->isOutputEnabled('TIG_PostNL');
    }
}
