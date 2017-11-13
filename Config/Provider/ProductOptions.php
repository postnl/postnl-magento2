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

class ProductOptions extends AbstractConfigProvider
{
    const XPATH_SUPPORTED_PRODUCT_OPTIONS               = 'tig_postnl/productoptions/supported_options';
    const XPATH_DEFAULT_PRODUCT_OPTION                  = 'tig_postnl/productoptions/default_option';
    const XPATH_DEFAULT_EVENING_PRODUCT_OPTION          = 'tig_postnl/productoptions/default_evening_option';
    const XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION      = 'tig_postnl/productoptions/default_extraathome_option';
    const XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION       = 'tig_postnl/productoptions/default_pakjegemak_option';
    const XPATH_DEFAULT_PAKJEGEMAK_EARLY_PRODUCT_OPTION = 'tig_postnl/productoptions/default_pakjegemak_early_option';
    const XPATH_DEFAULT_SUNDAY_PRODUCT_OPTION           = 'tig_postnl/productoptions/default_sunday_option';

    /**
     * @return string
     */
    public function getSupportedProductOptions()
    {
        return $this->getConfigFromXpath(self::XPATH_SUPPORTED_PRODUCT_OPTIONS);
    }

    /**
     * @return string|int
     */
    public function getDefaultProductOption()
    {
        return $this->getConfigFromXpath(self::XPATH_DEFAULT_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultEveningProductOption()
    {
        return $this->getConfigFromXpath(self::XPATH_DEFAULT_EVENING_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultExtraAtHomeProductOption()
    {
        return $this->getConfigFromXpath(self::XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultPakjeGemakProductOption()
    {
        return $this->getConfigFromXpath(self::XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultPakjeGemakEarlyProductOption()
    {
        return $this->getConfigFromXpath(self::XPATH_DEFAULT_PAKJEGEMAK_EARLY_PRODUCT_OPTION);
    }

    /**
     * @return string|int
     */
    public function getDefaultSundayProductOption()
    {
        return $this->getConfigFromXpath(self::XPATH_DEFAULT_SUNDAY_PRODUCT_OPTION);
    }
}
