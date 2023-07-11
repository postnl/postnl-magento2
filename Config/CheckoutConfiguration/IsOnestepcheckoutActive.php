<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;

/**
 * Some knockout situations work differently on onestepcheckouts.
 * An example is that totals should be recalculated on selecting a delivery option.
 *
 * Class IsOnestepcheckoutActive
 */
class IsOnestepcheckoutActive implements CheckoutConfigurationInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * IsOnestepcheckoutActive constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager              $moduleManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        if ($this->moduleManager->isEnabled('Onestepcheckout_Iosc') &&
            $this->scopeConfig->getValue('onestepcheckout_iosc/general/enable')
        ) {
            return true;
        }

        if ($this->moduleManager->isEnabled('Amasty_Checkout') &&
            $this->scopeConfig->getValue('amasty_checkout/general/enabled')) {
            return true;
        }

        return false;
    }
}
