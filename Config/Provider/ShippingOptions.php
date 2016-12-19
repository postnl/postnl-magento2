<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\Provider;

/**
 * Class ShippingOptions
 *
 * @package TIG\PostNL\Config\Provider
 */
class ShippingOptions extends AbstractConfigProvider
{
    const XPATH_SHIPPING_OPTION_ACITVE              = 'tig_postnl/shippingoptions/shippingoptions_active';
    const XPATH_SHIPPING_OPTION_STOCK               = 'tig_postnl/shippingoptions/stockoptions';
    const XPATH_SHIPPING_OPTION_DELIVERYDAYS_ACTIVE = 'tig_postnl/shippingoptions/deliverydays_active';
    const XPATH_SHIPPING_OPTION_MAX_DELIVERYDAYS    = 'tig_postnl/shippingoptions/max_deliverydays';
    const XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE   = 'tig_postnl/shippingoptions/pakjegemak_active';
    const XPATH_SHIPPING_OPTION_EVENING_ACTIVE      = 'tig_postnl/shippingoptions/eveningdelivery_active';
    const XPATH_SHIPPING_OPTION_EVENING_FEE         = 'tig_postnl/shippingoptions/eveningdelivery_fee';
    const XPATH_SHIPPING_OPTION_SUNDAY_ACTIVE       = 'tig_postnl/shippingoptions/sundaydelivery_active';
    const XPATH_SHIPPING_OPTION_SUNDAY_FEE          = 'tig_postnl/shippingoptions/sundaydelivery_fee';
    const XPATH_SHIPPING_OPTION_SEND_TRACKANDTRACE  = 'tig_postnl/shippingoptions/send_track_and_trace_email';

    protected $defaultMaxDeliverydays = '5';

    /**
     * @return mixed
     */
    public function isShippingoptionsActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_ACITVE);
    }

    /**
     * @return mixed
     */
    public function getShippingStockoptions()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_STOCK);
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function isPakjegemakActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE);
    }

    /**
     * @return mixed
     */
    public function isEveningDeliveryActive()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EVENING_ACTIVE);
    }

    /**
     * @return bool|mixed
     */
    public function getEveningDeliveryFee()
    {
        if (!$this->isEveningDeliveryActive()) {
            return '0';
        }

        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_EVENING_FEE);
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
    public function sendTrackAndTraceEmail()
    {
        return $this->getConfigFromXpath(self::XPATH_SHIPPING_OPTION_SEND_TRACKANDTRACE);
    }
}
