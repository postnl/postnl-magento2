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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;

/**
 * Class Data
 *
 * @package TIG\PostNL\Helper
 */
class Data extends AbstractHelper
{
    const PAKJEGEMAK_DELIVERY_OPTION = 'PG';
    const DAYTIME_DELIVERY_OPTION    = 'Daytime';
    const EVENING_DELIVERY_OPTION    = 'Evening';
    const SUNDAY_DELIVERY_OPTION     = 'Sunday';

    /**
     * @var TimezoneInterface
     */
    private $dateTime;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        TimezoneInterface $timezoneInterface,
        ShippingOptions $shippingOptions
    ) {
        $this->dateTime  = $timezoneInterface;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    public function isPostNLOrder(Order $order)
    {
        $shippingMethod = $order->getShippingMethod();

        return $shippingMethod == 'tig_postnl_regular';
    }

    /**
     * @return string
     */
    public function getCurrentTimeStamp()
    {
        $stamp = $this->dateTime->date();
        return $stamp->format('d-m-Y H:i:s');
    }

    /**
     * @param $date
     * @return \DateTime
     */
    public function getDateYmd($date = false)
    {
        $stamp = $this->dateTime->date();
        if ($date) {
            list($date) = explode(' ', $date);
            $stamp = $this->dateTime->date($date);
        }

        return $stamp->format('Y-m-d');
    }

    /**
     * @param $date
     * @return \DateTime
     */
    public function getDateDmy($date = false)
    {
        $stamp = $this->dateTime->date();
        if ($date) {
            list($date) = explode(' ', $date);
            $stamp = $this->dateTime->date($date);
        }

        return $stamp->format('d-m-Y');
    }

    /**
     * @return bool|string
     */
    public function getTommorowsDate()
    {
        $dateTime = $this->dateTime->date();
        return date('Y-m-d ' . $dateTime->format('H:i:s'), strtotime('tommorow'));
    }

    /**
     * @param $startDate
     *
     * @return string
     */
    public function getEndDate($startDate)
    {
        $maximumNumberOfDeliveryDays = 6;

        $endDate = $this->dateTime->date($startDate);
        // @codingStandardsIgnoreLine
        $endDate->add(new \DateInterval("P{$maximumNumberOfDeliveryDays}D"));

        return $endDate->format('d-m-Y');
    }

    /**
     * @return array
     */
    public function getAllowedDeliveryOptions()
    {
        $deliveryOptions = [];
        if ($this->shippingOptions->isPakjegemakActive()) {
            $deliveryOptions [] = self::PAKJEGEMAK_DELIVERY_OPTION;
        }

        return $deliveryOptions;
    }

    /**
     * @return array
     */
    public function getDeliveryTimeframesOptions()
    {
        $deliveryTimeframesOptions = [self::DAYTIME_DELIVERY_OPTION];

        if ($this->shippingOptions->isEveningDeliveryActive()) {
            $deliveryTimeframesOptions[] = self::EVENING_DELIVERY_OPTION;
        }

        if ($this->shippingOptions->isSundayDeliveryActive()) {
            $deliveryTimeframesOptions[] = self::SUNDAY_DELIVERY_OPTION;
        }

        return $deliveryTimeframesOptions;
    }

    /**
     * @param $xml
     *
     * @return string
     */
    public function formatXml($xml)
    {
        if (empty($xml)) {
            return '';
        }

        // @codingStandardsIgnoreLine
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($xml);
        $domDocument->formatOutput = true;

        return $domDocument->saveXML();
    }
}
