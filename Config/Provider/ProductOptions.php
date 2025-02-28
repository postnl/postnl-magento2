<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * This class contains all configuration options related to the product options.
 * This will cause that it is too long for Code Sniffer to check.
 *
 * @codingStandardsIgnoreStart
 */
class ProductOptions extends AbstractConfigProvider
{
//    const XPATH_SUPPORTED_PRODUCT_OPTIONS               = 'tig_postnl/delivery_settings/supported_options';
    const XPATH_DEFAULT_PRODUCT_OPTION                        = 'tig_postnl/delivery_settings/default_option';
    const XPATH_DEFAULT_BE_DOMESTIC_OPTION                    = 'tig_postnl/delivery_settings/default_be_domestic_option';
    const XPATH_DEFAULT_BE_NL_OPTION                          = 'tig_postnl/delivery_settings/default_be_nl_option';
    const XPATH_USE_ALTERNATIVE_DEFAULT_OPTION                = 'tig_postnl/delivery_settings/use_alternative_default';
    const XPATH_ALTERNATIVE_DELIVERY_MAP                      = 'tig_postnl/delivery_settings/alternative_delivery_map';
    const XPATH_DEFAULT_EVENING_PRODUCT_OPTION                = 'tig_postnl/evening_delivery_nl/default_evening_option';
    const XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION            = 'tig_postnl/extra_at_home/default_extraathome_option';
    const XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION             = 'tig_postnl/post_offices/default_pakjegemak_option';
    const XPATH_DEFAULT_PAKJEGEMAK_USE_ALTERNATIVE            = 'tig_postnl/post_offices/use_alternative_pakjegemak';
    const XPATH_DEFAULT_PAKJEGEMAK_ALTERNATIVE_MAP            = 'tig_postnl/post_offices/alternative_pakjegemak_map';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_PRODUCT_OPTION          = 'tig_postnl/post_offices/default_pakjegemak_be_option';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_PRODUCT_OPTION = 'tig_postnl/post_offices/default_pakjegemak_be_domestic_option';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_USE_ALTERNATIVE = 'tig_postnl/post_offices/use_alternative_pakjegemak_be_domestic';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_ALTERNATIVE_MAP = 'tig_postnl/post_offices/alternative_pakjegemak_be_domestic_map';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_NL_PRODUCT_OPTION       = 'tig_postnl/post_offices/default_pakjegemak_be_nl_option';
    const XPATH_DEFAULT_PAKJEGEMAK_GLOBAL_PRODUCT_OPTION      = 'tig_postnl/post_offices/default_pakjegemak_global_option';
    const XPATH_DEFAULT_EVENING_BE_PRODUCT_OPTION             = 'tig_postnl/evening_delivery_be/default_evening_be_option';
    const XPATH_DEFAULT_BE_PRODUCT_OPTION                     = 'tig_postnl/delivery_settings/default_be_option';
    const XPATH_DEFAULT_BE_USE_ALTERNATIVE                    = 'tig_postnl/delivery_settings/use_alternative_be';
    const XPATH_DEFAULT_BE_ALTERNATIVE_MAP                    = 'tig_postnl/delivery_settings/alternative_be_map';
    const XPATH_DEFAULT_EPS_PRODUCT_OPTION                    = 'tig_postnl/delivery_settings/default_eps_option';
    const XPATH_DEFAULT_EPS_USE_ALTERNATIVE                   = 'tig_postnl/delivery_settings/use_alternative_eps';
    const XPATH_DEFAULT_EPS_ALTERNATIVE_MAP                   = 'tig_postnl/delivery_settings/alternative_eps_map';
    const XPATH_DEFAULT_EPS_BUSINESS_PRODUCT_OPTION           = 'tig_postnl/delivery_settings/default_eps_business_option';
    const XPATH_DEFAULT_PEPS_PRODUCT_OPTION                   = 'tig_postnl/peps/default_peps_option';
    const XPATH_DEFAULT_GP_PRODUCT_OPTION                     = 'tig_postnl/globalpack/default_gp_option';
    const XPATH_DEFAULT_GP_USE_ALTERNATIVE                    = 'tig_postnl/globalpack/use_alternative_gp';
    const XPATH_DEFAULT_GP_ALTERNATIVE_MAP                    = 'tig_postnl/globalpack/alternative_gp_map';
    const XPATH_DEFAULT_DEFAULT_DELIVERY_STATED_ADDRESS       = 'tig_postnl/delivery_settings/default_delivery_stated_address';
    const XPATH_DEFAULT_DEFAULT_DELIVERY_STATED_ADDRESS_BE    = 'tig_postnl/delivery_settings/default_delivery_stated_address_be';
    const XPATH_DEFAULT_PEPS_BOXABLE_PACKETS                  = 'tig_postnl/peps/default_peps_boxable_packets_option';

    private Json $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        \TIG\PostNL\Config\Source\Options\ProductOptions $productOptions,
        Json $serializer
    ) {
        parent::__construct($scopeConfig, $moduleManager, $crypt, $productOptions);
        $this->serializer = $serializer;
    }

    /**
     * Since 1.5.1 all product options are automaticly supported.
     * @return array
     */
    public function getSupportedProductOptions()
    {
        return $this->productOptions->getAllProductCodes();
    }

    /**
     * @return string|int
     */
    public function getDefaultProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PRODUCT_OPTION);
    }

    public function getUseAlternativeDefault(): bool
    {
        return (bool)$this->getConfigFromXpath(static::XPATH_USE_ALTERNATIVE_DEFAULT_OPTION);
    }

    public function getAlternativeMap(string $config): array
    {
        $value = $this->getConfigFromXpath($config);
        try {
            if ($value) {
                $value = $this->serializer->unserialize($value);
            }
        } catch (\Exception $e) {
            $value = [];
        }
        return is_array($value) ? $value : [];
    }

    public function getAlternativeDeliveryMap(): array
    {
        if (!$this->getUseAlternativeDefault()) {
            return [];
        }

        return $this->getAlternativeMap(static::XPATH_ALTERNATIVE_DELIVERY_MAP);
    }

    /**
     * @param string $country
     *
     * @return string|int
     */
    public function getDefaultEveningProductOption($country = null)
    {
        if ($country === 'BE') {
            return $this->getDefaultEveningBeProductOption();
        }

        return $this->getConfigFromXpath(static::XPATH_DEFAULT_EVENING_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultExtraAtHomeProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultEveningBeProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_EVENING_BE_PRODUCT_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultBeDomesticProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_BE_DOMESTIC_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultBeNlProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_BE_NL_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultPakjeGemakBeProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_BE_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultPakjeGemakBeNlProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_BE_NL_PRODUCT_OPTION);
    }

    public function getDefaultPakjeGemakGlobalProductOption(): int
    {
        return (int)$this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_GLOBAL_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultPakjeGemakBeDomesticProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_PRODUCT_OPTION);
    }

    public function getUseAlternativePakjeGemakBeDomestic(): bool
    {
        return (bool)$this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_USE_ALTERNATIVE);
    }

    public function getAlternativePakjeGemakBeDomesticMap(): array
    {
        if (!$this->getUseAlternativePakjeGemakBeDomestic()) {
            return [];
        }
        return $this->getAlternativeMap(static::XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_ALTERNATIVE_MAP);
    }

    /**
     * @return mixed
     */
    public function getDefaultBeProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_BE_PRODUCT_OPTION);
    }

    public function getUseAlternativeBe(): bool
    {
        return (bool)$this->getConfigFromXpath(static::XPATH_DEFAULT_BE_USE_ALTERNATIVE);
    }

    public function getAlternativeBeMap(): array
    {
        if (!$this->getUseAlternativeBe()) {
            return [];
        }
        return $this->getAlternativeMap(static::XPATH_DEFAULT_BE_ALTERNATIVE_MAP);
    }

    /**
     * @return mixed
     */
    public function getDefaultEpsProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_EPS_PRODUCT_OPTION);
    }

    public function getUseAlternativeEps(): bool
    {
        return (bool)$this->getConfigFromXpath(static::XPATH_DEFAULT_EPS_USE_ALTERNATIVE);
    }

    public function getAlternativeEpsMap(): array
    {
        if (!$this->getUseAlternativeEps()) {
            return [];
        }
        return $this->getAlternativeMap(static::XPATH_DEFAULT_EPS_ALTERNATIVE_MAP);
    }

    /**
     * @return mixed
     */
    public function getDefaultEpsBusinessProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_EPS_BUSINESS_PRODUCT_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultPepsProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PEPS_PRODUCT_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultGlobalpackOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_GP_PRODUCT_OPTION);
    }

    public function getUseAlternativeGlobalpack(): bool
    {
        return (bool)$this->getConfigFromXpath(static::XPATH_DEFAULT_GP_USE_ALTERNATIVE);
    }

    public function getAlternativeGlobalpackMap(): array
    {
        if (!$this->getUseAlternativeGlobalpack()) {
            return [];
        }
        return $this->getAlternativeMap(static::XPATH_DEFAULT_GP_ALTERNATIVE_MAP);
    }

    /**
     * @return mixed
     */
    public function getDefaultPakjeGemakProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION);
    }

    public function getUseAlternativePakjegemak(): bool
    {
        return (bool)$this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_USE_ALTERNATIVE);
    }

    public function getAlternativePakjegemakMap(): array
    {
        if (!$this->getUseAlternativePakjegemak()) {
            return [];
        }
        return $this->getAlternativeMap(static::XPATH_DEFAULT_PAKJEGEMAK_ALTERNATIVE_MAP);
    }

    /**
     * @param $code
     *
     * @return null|string
     */
    public function getGuaranteedType($code)
    {
        return $this->productOptions->getGuaranteedType($code);
    }

    /**
     * @param $code
     * @param $key
     * @param $value
     *
     * @return bool|null
     */
    public function checkProductByFlags($code, $key, $value)
    {
        return $this->productOptions->doesProductMatchFlags($code, $key, $value);
    }

    /**
     * @return mixed
     */
    public function getDefaultStatedAddressOnlyProductOption($country, $shopCountry)
    {
        if ($shopCountry === 'BE') {
            return $this->getConfigFromXpath(static::XPATH_DEFAULT_DEFAULT_DELIVERY_STATED_ADDRESS_BE);
        }

        if ($country === 'BE' && $shopCountry === 'NL') {
            return $this->productOptions->getOptionsByCode('4941')['value'];
        }

        return $this->getConfigFromXpath(static::XPATH_DEFAULT_DEFAULT_DELIVERY_STATED_ADDRESS);
    }

    /**
     * @return string
     */
    public function getDefaultLetterboxPackageProductOption()
    {
        $result = array_column($this->productOptions->getProductOptions(['group' => 'buspakje_options']), 'value');
        return reset($result);
    }

    /**
     * @return string
     */
    public function getDefaultBoxablePacketsProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PEPS_BOXABLE_PACKETS);
    }
}
/**
 * codingStandardsIgnoreEnd
 */
