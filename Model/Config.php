<?php
declare(strict_types=1);

namespace TIG\PostNL\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const CONFIG_PATH_ENABLED = 'tig_postnl/fillin/settings/enabled';
    private const CONFIG_PATH_CLIENT_ID = 'tig_postnl/fillin/settings/client_id';
    private const CONFIG_PATH_DEBUG = 'tig_postnl/fillin/settings/debug';

    public const CONFIG_PATH_ENABLED_IN_CART = 'tig_postnl/fillin/settings/cart/enabled';
    private const CONFIG_PATH_CART_DISPLAY = 'tig_postnl/fillin/settings/cart/display';

    public const CONFIG_PATH_ENABLED_IN_CHECKOUT = 'tig_postnl/fillin/settings/checkout/enabled';
    private const CONFIG_PATH_CHECKOUT_DISPLAY = 'tig_postnl/fillin/settings/checkout/display';

    public const CONFIG_PATH_ENABLED_IN_MINICART = 'tig_postnl/fillin/settings/minicart/enabled';
    private const CONFIG_PATH_MINICART_DISPLAY = 'tig_postnl/fillin/settings/minicart/display';

    private const CONFIG_PATH_API_KEY = 'tig_postnl/generalconfiguration_extension_status/api_key';
    private const CONFIG_PATH_API_KEY_TEST = 'tig_postnl/generalconfiguration_extension_status/api_key_test';

    private const CONFIG_PATH_CUSTOMER_NUMBER = 'tig_postnl/generalconfiguration_extension_status/customer_number';
    private const CONFIG_PATH_CUSTOMER_NUMBER_TEST = 'tig_postnl/generalconfiguration_extension_status/customer_number_test';

    private const CONFIG_PATH_CUSTOMER_CODE = 'tig_postnl/generalconfiguration_extension_status/customer_code';
    private const CONFIG_PATH_CUSTOMER_CODE_TEST = 'tig_postnl/generalconfiguration_extension_status/customer_code_test';

    public const POSTNL_STATE_ATTRIBUTE = 'postnl_state';
    public const POSTNL_VERIFIER_ATTRIBUTE = 'postnl_verifier';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED, ScopeInterface::SCOPE_STORES);
    }

    public function isEnabledInCart(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED_IN_CART, ScopeInterface::SCOPE_STORES);
    }

    public function getCartDisplayPosition(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_CART_DISPLAY, ScopeInterface::SCOPE_STORES);
    }

    public function isEnabledInCheckout(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED_IN_CHECKOUT, ScopeInterface::SCOPE_STORES);
    }

    public function getCheckoutDisplayPosition(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_CHECKOUT_DISPLAY, ScopeInterface::SCOPE_STORES);
    }

    public function isEnabledInMiniCart(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED_IN_MINICART, ScopeInterface::SCOPE_STORES);
    }

    public function getMiniCartDisplayPosition(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_MINICART_DISPLAY, ScopeInterface::SCOPE_STORES);
    }

    public function getClientID(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_CLIENT_ID, ScopeInterface::SCOPE_STORES);
    }

    public function debugOn(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_DEBUG, ScopeInterface::SCOPE_STORES);
    }
}
