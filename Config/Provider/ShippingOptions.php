<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\SerializerInterface;
use TIG\PostNL\Config\Source\Options\ProductOptions;

/**
 * @codingStandardsIgnoreStart
 */
class ShippingOptions extends AbstractConfigProvider
{
    const XPATH_SHIPPING_OPTION_ACITVE                   = 'tig_postnl/delivery_settings/shippingoptions_active';
    const XPATH_GUARANTEED_DELIVERY_ACTIVE               = 'tig_postnl/delivery_settings/guaranteed_delivery';
    const XPATH_SHIPPING_OPTION_STOCK                    = 'tig_postnl/stock_settings/stockoptions';
    const XPATH_SHIPPING_OPTION_DELIVERYDAYS_ACTIVE      = 'tig_postnl/delivery_days/deliverydays_active';
    const XPATH_SHIPPING_OPTION_MAX_DELIVERYDAYS         = 'tig_postnl/delivery_days/max_deliverydays';
    const XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE        = 'tig_postnl/post_offices/pakjegemak_active';
    const XPATH_SHIPPING_OPTION_PAKJEGEMAK_DEFAULT        = 'tig_postnl/post_offices/pakjegemak_default';
    const XPATH_SHIPPING_OPTION_PAKJEGEMAK_BE_ACTIVE     = 'tig_postnl/post_offices/pakjegemak_be_active';
    const XPATH_SHIPPING_OPTION_SHOW_PACKAGE_MACHINES    = 'tig_postnl/post_offices/show_package_machines';
    const XPATH_SHIPPING_OPTION_EVENING_ACTIVE           = 'tig_postnl/evening_delivery_nl/eveningdelivery_active';
    const XPATH_SHIPPING_OPTION_EVENING_BE_ACTIVE        = 'tig_postnl/evening_delivery_be/eveningdelivery_be_active';
    const XPATH_SHIPPING_OPTION_EVENING_FEE              = 'tig_postnl/evening_delivery_nl/eveningdelivery_fee';
    const XPATH_SHIPPING_OPTION_EXTRAATHOME_ACTIVE       = 'tig_postnl/extra_at_home/extraathome_active';
    const XPATH_SHIPPING_OPTION_EVENING_BE_FEE           = 'tig_postnl/evening_delivery_be/eveningdelivery_be_fee';
    const XPATH_SHIPPING_OPTION_SUNDAY_ACTIVE            = 'tig_postnl/sunday_delivery/sundaydelivery_active';
    const XPATH_SHIPPING_OPTION_SUNDAY_FEE               = 'tig_postnl/sunday_delivery/sundaydelivery_fee';
    const XPATH_SHIPPING_OPTION_TODAY_ACTIVE             = 'tig_postnl/today_delivery/todaydelivery_active';
    const XPATH_SHIPPING_OPTION_TODAY_FEE                = 'tig_postnl/today_delivery/todaydelivery_fee';
    const XPATH_SHIPPING_OPTION_TODAY_CUTOFF             = 'tig_postnl/today_delivery/cutoff_time';
    const XPATH_SHIPPING_OPTION_SEND_TRACKANDTRACE       = 'tig_postnl/track_and_trace/send_track_and_trace_email';
    const XPATH_SHIPPING_OPTION_DELIVERY_DELAY           = 'tig_postnl/track_and_trace/delivery_delay';
    const XPATH_SHIPPING_OPTION_IDCHECK_ACTIVE           = 'tig_postnl/id_check/idcheck_active';
    const XPATH_ITEM_OPTIONS_MANAGE_STOCK                = 'cataloginventory/item_options/manage_stock';
    const XPATH_SHIPPING_OPTION_CARGO_ACTIVE             = 'tig_postnl/cargo/cargo_active';
    const XPATH_SHIPPING_OPTION_EPS_BUSINESS_ACTIVE      = 'tig_postnl/eps/business_active';
    const XPATH_SHIPPING_OPTIONS_PEPS_ACTIVE             = 'tig_postnl/peps/active';
    const XPATH_SHIPPING_OPTIONS_GLOBALPACK_ACTIVE       = 'tig_postnl/globalpack/enabled';
    const XPATH_SHIPPING_OPTION_STATED_ADDRESS_ACTIVE    = 'tig_postnl/delivery_settings/stated_address_only_active';
    const XPATH_SHIPPING_OPTION_STATED_ADDRESS_FEE       = 'tig_postnl/delivery_settings/stated_address_only_fee';
    const XPATH_SHIPPING_OPTION_LETTERBOX_PACKAGE_ACTIVE = 'tig_postnl/letterbox_package/letterbox_package_active';
    const XPATH_SHIPPING_OPTION_BOXABLE_PACKETS_ACTIVE   = 'tig_postnl/peps/peps_boxable_packets_active';
    const XPATH_SHIPPING_OPTION_COUNTRY                  = 'tig_postnl/generalconfiguration_shipping_address/country';
    const XPATH_SHIPPING_OPTION_INSURED_TIER             = 'tig_postnl/insured_delivery/insured_tier';
    const XPATH_SHIPPING_OPTION_DELIVERY_DATE_OFF        = 'tig_postnl/delivery_days/delivery_date_off';

    private $defaultMaxDeliverydays = '5';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager $moduleManager
     * @param Encryptor $crypt
     * @param ProductOptions $productOptions
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        ProductOptions $productOptions,
        SerializerInterface $serializer
    ) {
        parent::__construct($scopeConfig, $moduleManager, $crypt, $productOptions);
        $this->serializer = $serializer;
    }

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
    public function isPakjegemakDefault(string $country = 'NL'): bool
    {
        if (!$this->isPakjegemakActive($country)) {
            return false;
        }
        return (bool)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_PAKJEGEMAK_DEFAULT);
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
    public function isTodayDeliveryActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_TODAY_ACTIVE);
    }

    /**
     * @return mixed|string
     */
    public function getTodayDeliveryFee()
    {
        if (!$this->isTodayDeliveryActive()) {
            return '0';
        }

        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_TODAY_FEE);
    }

    /**
     * @return mixed
     */
    public function getTodayCutoffTime()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_TODAY_CUTOFF);
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
     * Points to: International Packets and Boxable Packets > Use International (Mailbox) Packets
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
     * @return mixed
     */
    public function isPackageMachineFilterActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_SHOW_PACKAGE_MACHINES);
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
    public function isBoxablePacketsActive()
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_BOXABLE_PACKETS_ACTIVE);
    }

    /**
     * @return bool
     */
    public function canUseBeProducts()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_COUNTRY) === 'BE';
    }

    /**
     * @return string|int
     */
    public function getInsuredTier()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_INSURED_TIER);
    }

    /**
     * @param string $configPath
     * @return array
     */
    public function getDeliveryOff(string $configPath = self::XPATH_SHIPPING_OPTION_DELIVERY_DATE_OFF): array
    {
        try {
            return $configPath === self::XPATH_SHIPPING_OPTION_DELIVERY_DATE_OFF
                ? $this->serializer->unserialize((string)$this->getConfigFromXpath($configPath))
                : explode(',', (string)$this->getConfigFromXpath($configPath));
        } catch (\Throwable $e) {
            return [];
        }
    }
}
/**
 * @codingStandardsIgnoreEnd
 */
