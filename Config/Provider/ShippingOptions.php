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
 * @codingStandardsIgnoreStart
 */
class ShippingOptions extends AbstractConfigProvider
{
    const XPATH_SHIPPING_OPTION_ACITVE                    = 'tig_postnl/delivery_settings/shippingoptions_active';
    const XPATH_GUARANTEED_DELIVERY_ACTIVE                = 'tig_postnl/delivery_settings/guaranteed_delivery';
    const XPATH_SHIPPING_OPTION_STOCK                     = 'tig_postnl/stock_settings/stockoptions';
    const XPATH_SHIPPING_OPTION_DELIVERYDAYS_ACTIVE       = 'tig_postnl/delivery_days/deliverydays_active';
    const XPATH_SHIPPING_OPTION_MAX_DELIVERYDAYS          = 'tig_postnl/delivery_days/max_deliverydays';
    const XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE         = 'tig_postnl/post_offices/pakjegemak_active';
    const XPATH_SHIPPING_OPTION_PAKJEGEMAK_BE_ACTIVE      = 'tig_postnl/post_offices/pakjegemak_be_active';
    const XPATH_SHIPPING_OPTION_EVENING_ACTIVE            = 'tig_postnl/evening_delivery_nl/eveningdelivery_active';
    const XPATH_SHIPPING_OPTION_EVENING_BE_ACTIVE         = 'tig_postnl/evening_delivery_be/eveningdelivery_be_active';
    const XPATH_SHIPPING_OPTION_EVENING_FEE               = 'tig_postnl/evening_delivery_nl/eveningdelivery_fee';
    const XPATH_SHIPPING_OPTION_EXTRAATHOME_ACTIVE        = 'tig_postnl/extra_at_home/extraathome_active';
    const XPATH_SHIPPING_OPTION_EVENING_BE_FEE            = 'tig_postnl/evening_delivery_be/eveningdelivery_be_fee';
    const XPATH_SHIPPING_OPTION_SUNDAY_ACTIVE             = 'tig_postnl/sunday_delivery/sundaydelivery_active';
    const XPATH_SHIPPING_OPTION_SUNDAY_FEE                = 'tig_postnl/sunday_delivery/sundaydelivery_fee';
    const XPATH_SHIPPING_OPTION_SEND_TRACKANDTRACE        = 'tig_postnl/track_and_trace/send_track_and_trace_email';
    const XPATH_SHIPPING_OPTION_DELIVERY_DELAY            = 'tig_postnl/track_and_trace/delivery_delay';
    const XPATH_SHIPPING_OPTION_IDCHECK_ACTIVE            = 'tig_postnl/id_check/idcheck_active';
    const XPATH_ITEM_OPTIONS_MANAGE_STOCK                 = 'cataloginventory/item_options/manage_stock';
    const XPATH_SHIPPING_OPTION_CARGO_ACTIVE              = 'tig_postnl/cargo/cargo_active';
    const XPATH_SHIPPING_OPTION_EPS_BUSINESS_ACTIVE       = 'tig_postnl/eps/business_active';
    const XPATH_SHIPPING_OPTIONS_PEPS_ACTIVE              = 'tig_postnl/peps/active';
    const XPATH_SHIPPING_OPTIONS_GLOBALPACK_ACTIVE        = 'tig_postnl/globalpack/enabled';
    const XPATH_SHIPPING_OPTION_STATED_ADDRESS_ACTIVE     = 'tig_postnl/delivery_settings/stated_address_only_active';
    const XPATH_SHIPPING_OPTION_STATED_ADDRESS_FEE        = 'tig_postnl/delivery_settings/stated_address_only_fee';
    const XPATH_SHIPPING_OPTION_LETTERBOX_PACKAGE_ACTIVE  = 'tig_postnl/letterbox_package/letterbox_package_active';
    const XPATH_SHIPPING_OPTION_COUNTRY                   = 'tig_postnl/generalconfiguration_shipping_address/country';

    private $defaultMaxDeliverydays = '5';

    /**
     * @return bool
     */
    public function isShippingoptionsActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_ACITVE);
    }

    /**
     * @return bool
     */
    public function isGuaranteedDeliveryActive()
    {
        return (bool) $this->getConfigFromXpath( static::XPATH_GUARANTEED_DELIVERY_ACTIVE);
    }

    /**
     * @return string
     */
    public function getShippingStockoptions()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_STOCK);
    }

    /**
     * @return bool
     */
    public function isDeliverydaysActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_DELIVERYDAYS_ACTIVE);
    }

    /**
     * @return mixed|string
     */
    public function getMaxAmountOfDeliverydays()
    {
        if (!$this->isDeliverydaysActive()) {
            return $this->defaultMaxDeliverydays;
        }

        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_MAX_DELIVERYDAYS);
    }

    /**
     * @param string $country
     * @return mixed
     */
    public function isPakjegemakActive($country = 'NL')
    {
        if ('BE' === $country){
            return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_PAKJEGEMAK_BE_ACTIVE);
        }
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE);
    }

    /**
     * @param string $country
     * @return mixed
     */
    public function isEveningDeliveryActive($country = 'NL')
    {
        if ('NL' === $country) {
            return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EVENING_ACTIVE);
        }

        if ('BE' === $country){
            return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EVENING_BE_ACTIVE);
        }

        return false;
    }

    /**
     * @param string $country
     * @return bool|mixed
     */
    public function getEveningDeliveryFee($country = 'NL')
    {
        if (!$this->isEveningDeliveryActive($country)) {
            return '0';
        }

        if ('NL' === $country) {
            return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EVENING_FEE);
        }

        if ('BE' === $country){
            return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EVENING_BE_FEE);
        }

        return 0;
    }

    /**
     * @return bool
     */
    public function isExtraAtHomeActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EXTRAATHOME_ACTIVE);
    }

    /**
     * @return bool
     */
    public function isIDCheckActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_IDCHECK_ACTIVE);
    }

    /**
     * @return mixed
     */
    public function isSundayDeliveryActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_SUNDAY_ACTIVE);
    }

    /**
     * @return mixed|string
     */
    public function getSundayDeliveryFee()
    {
        if (!$this->isSundayDeliveryActive()) {
            return '0';
        }

        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_SUNDAY_FEE);
    }

    /**
     * @return mixed
     */
    public function getDeliveryDelay()
    {
        return (int)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_DELIVERY_DELAY);
    }

    /**
     * @return bool
     */
    public function getManageStock()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_ITEM_OPTIONS_MANAGE_STOCK);
    }

    /**
     * @return bool
     */
    public function canUseCargoProducts()
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_SHIPPING_OPTION_CARGO_ACTIVE);
    }

    /**
     * @return bool
     */
    public function canUseEpsBusinessProducts()
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_SHIPPING_OPTION_EPS_BUSINESS_ACTIVE);
    }

    /**
     * @return bool
     */
    public function canUsePriority()
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_SHIPPING_OPTIONS_PEPS_ACTIVE);
    }

    /**
     * @return bool
     */
    public function canUseGlobalPack()
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_SHIPPING_OPTIONS_GLOBALPACK_ACTIVE);
    }

    /**
     * @return bool
     */
    public function isStatedAddressOnlyActive()
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_SHIPPING_OPTION_STATED_ADDRESS_ACTIVE);
    }

    /**
     * @return float
     */
    public function getStatedAddressOnlyFee()
    {
        if (!$this->isStatedAddressOnlyActive()) {
            return (float)0.0;
        }

        return (float) $this->getConfigFromXpath(static::XPATH_SHIPPING_OPTION_STATED_ADDRESS_FEE);
    }

    /**
     * @return bool
     */
    public function isLetterboxPackageActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_LETTERBOX_PACKAGE_ACTIVE);
    }

    /**
     * @return bool
     */
    public function canUseBeProducts()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_COUNTRY) === 'BE';
    }
}
/**
 * @codingStandardsIgnoreEnd
 */
