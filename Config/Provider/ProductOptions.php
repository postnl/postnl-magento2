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
    const XPATH_USE_ALTERNATIVE_DEFAULT_OPTION                = 'tig_postnl/delivery_settings/use_alternative_default';
    const XPATH_ALTERNATIVE_DEFAULT_MIN_AMOUNT                = 'tig_postnl/delivery_settings/alternative_default_min_amount';
    const XPATH_ALTERNATIVE_DEFAULT_PRODUCT_OPTION            = 'tig_postnl/delivery_settings/alternative_default_option';
    const XPATH_DEFAULT_EVENING_PRODUCT_OPTION                = 'tig_postnl/evening_delivery_nl/default_evening_option';
    const XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION            = 'tig_postnl/extra_at_home/default_extraathome_option';
    const XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION             = 'tig_postnl/post_offices/default_pakjegemak_option';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_PRODUCT_OPTION          = 'tig_postnl/post_offices/default_pakjegemak_be_option';
    const XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_PRODUCT_OPTION = 'tig_postnl/post_offices/default_pakjegemak_be_domestic_option';
    const XPATH_DEFAULT_EVENING_BE_PRODUCT_OPTION             = 'tig_postnl/evening_delivery_be/default_evening_be_option';
    const XPATH_DEFAULT_BE_PRODUCT_OPTION                     = 'tig_postnl/delivery_settings/default_be_option';
    const XPATH_DEFAULT_SUNDAY_PRODUCT_OPTION                 = 'tig_postnl/sunday_delivery/default_sunday_option';
    const XPATH_DEFAULT_TODAY_PRODUCT_OPTION                  = 'tig_postnl/today_delivery/default_today_option';
    const XPATH_DEFAULT_CARGO_DELIVERY_TYPE                   = 'tig_postnl/delivery_settings/default_cargo_type';
    const XPATH_ALTERNATIVE_DEFAULT_CARGO_DELIVERY_TYPE       = 'tig_postnl/delivery_settings/alternative_cargo_type';
    const XPATH_DEFAULT_PACKAGE_DELIVERY_TYPE                 = 'tig_postnl/delivery_settings/default_package_type';
    const XPATH_ALTERNATIVE_DEFAULT_PACKAGE_DELIVERY_TYPE     = 'tig_postnl/delivery_settings/alternative_package_type';
    const XPATH_DEFAULT_EPS_PRODUCT_OPTION                    = 'tig_postnl/delivery_settings/default_eps_option';
    const XPATH_DEFAULT_GP_PRODUCT_OPTION                     = 'tig_postnl/globalpack/default_gp_option';
    const XPATH_DEFAULT_DEFAULT_DELIVERY_STATED_ADDRESS       = 'tig_postnl/delivery_settings/default_delivery_stated_address';
    const XPATH_DEFAULT_DEFAULT_DELIVERY_STATED_ADDRESS_BE    = 'tig_postnl/delivery_settings/default_delivery_stated_address_be';

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

    /**
     * @return string|int
     */
    public function getUseAlternativeDefault()
    {
        return $this->getConfigFromXpath(static::XPATH_USE_ALTERNATIVE_DEFAULT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getAlternativeDefaultMinAmount()
    {
        if (!$this->getUseAlternativeDefault()) {
            return '0';
        }

        return $this->getConfigFromXpath(static::XPATH_ALTERNATIVE_DEFAULT_MIN_AMOUNT);
    }

    /**
     * @return string|int|bool
     */
    public function getAlternativeDefaultProductOption()
    {
        if (!$this->getUseAlternativeDefault()) {
            return false;
        }

        return $this->getConfigFromXpath(static::XPATH_ALTERNATIVE_DEFAULT_PRODUCT_OPTION);
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
     * @return string|int
     */
    public function getDefaultPakjeGemakBeProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_BE_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultPakjeGemakBeDomesticProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_PRODUCT_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultBeProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_BE_PRODUCT_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultEpsProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_EPS_PRODUCT_OPTION);
    }

    /**
     * @return mixed
     */
    public function getDefaultGlobalpackOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_GP_PRODUCT_OPTION);
    }

    /**
     * @param string $country
     * @return mixed
     */
    public function getDefaultPakjeGemakProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultSundayProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_SUNDAY_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultTodayProductOption()
    {
        return $this->getConfigFromXpath(static::XPATH_DEFAULT_TODAY_PRODUCT_OPTION);
    }

    /**
     * @return string
     */
    public function getDefaultGuaranteedPackageDeliveryType()
    {
        return (string) $this->getConfigFromXpath(static::XPATH_DEFAULT_PACKAGE_DELIVERY_TYPE);
    }

    /**
     * @return string
     */
    public function getDefaultGuaranteedCargoDeliveryType()
    {
        return (string) $this->getConfigFromXpath(static::XPATH_DEFAULT_CARGO_DELIVERY_TYPE);
    }

    /**
     * @return string
     */
    public function getDefaultAlternativeGuaranteedPackageDeliveryType()
    {
        return (string) $this->getConfigFromXpath(static::XPATH_ALTERNATIVE_DEFAULT_PACKAGE_DELIVERY_TYPE);
    }

    /**
     * @return string
     */
    public function getDefaultAlternativeGuaranteedCargoDeliveryType()
    {
        return (string) $this->getConfigFromXpath(static::XPATH_ALTERNATIVE_DEFAULT_CARGO_DELIVERY_TYPE);
    }

    /**
     * @param bool   $alternative
     * @param string $type
     *
     * @return string
     */
    public function getGuaranteedDeliveryType($alternative = false, $type = 'package')
    {
        if ($alternative && $type == 'package') {
            return $this->getDefaultAlternativeGuaranteedPackageDeliveryType();
        }

        if ($alternative) {
            return $this->getDefaultAlternativeGuaranteedCargoDeliveryType();
        }

        if (!$alternative && $type == 'package') {
            return $this->getDefaultGuaranteedPackageDeliveryType();
        }

        return $this->getDefaultGuaranteedCargoDeliveryType();
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
}
/**
 * codingStandardsIgnoreEnd
 */
