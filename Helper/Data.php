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
namespace TIG\PostNL\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\Webshop;

/**
 * @codingStandardsIgnoreStart
 * @todo : Split this helper to service classes. Now the code is ignored because of the 11 methods.
 *       1 .All the date methods should be placed within a date service
 *       2. Remove the codingStandardsIgnore line an if posible the whole class.
 * @codingStandardsIgnoreEnd
 */
// @codingStandardsIgnoreFile
class Data extends AbstractHelper
{
    const PACKAGEBOX_DELIVERY_OPTION         = 'PG';
    const DISABLE_PACKAGEBOX_DELIVERY_OPTION = 'PG_EX';

    /**
     * @var TimezoneInterface
     */
    private $dateTime;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var Webshop
     */
    private $webshop;

    /**
     * @var null
     */
    private $currentDate = null;

    /**
     * @param TimezoneInterface $timezoneInterface
     * @param ShippingOptions   $shippingOptions
     * @param Webshop           $webshop
     */
    public function __construct(
        TimezoneInterface $timezoneInterface,
        ShippingOptions $shippingOptions,
        Webshop $webshop
    ) {
        $this->dateTime  = $timezoneInterface;
        $this->shippingOptions = $shippingOptions;
        $this->webshop = $webshop;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    public function isPostNLOrder(Order $order)
    {
        $shippingMethod = $order->getShippingMethod();
        $allowedMethods = $this->webshop->getAllowedShippingMethods($order->getStoreId());

        if (in_array($shippingMethod, $allowedMethods)) {
            return true;
        }

        return $shippingMethod == 'tig_postnl_regular';
    }

    /**
     * @param $timeOnly
     * @return string
     */
    public function getCurrentTimeStamp($timeOnly = false)
    {
        $stamp = $this->dateTime->date($this->getCurrentDate());
        if ($timeOnly) {
            return $stamp->format('H:i:s');
        }

        return $stamp->format('d-m-Y H:i:s');
    }

    /**
     * @param \DateTime|bool|object $date
     * @param $format
     * @return string
     */
    public function getDate($date = false, $format = 'Y-m-d')
    {
        $stamp = $this->dateTime->date();
        if ($date) {
            list($date) = explode(' ', $date);
            $stamp = $this->dateTime->date($date);
        }

        return $stamp->format($format);
    }

    /**
     * Get the number of the day inside a week or the number of the week inside the Year.
     * day number format == 'w', week number format == 'W'
     *
     * @param        $date
     * @param string $format
     *
     * @return int
     */
    public function getDayOrWeekNumber($date, $format = 'w')
    {
        $number = date($format, $this->getCurrentDate());

        if ($date) {
            $number = date($format, strtotime($date));
        }

        if ($format === 'w') {
            return $this->formatDayNumber($number);
        }

        return $number;
    }

    /**
     * Make sure that day 7 (Sunday) is actually returned as 7 rather than 0
     *
     * @param $number
     *
     * @return int
     */
    private function formatDayNumber($number)
    {
        $formattedNumber = $number % 7;

        if ($formattedNumber == 0) {
            $formattedNumber = 7;
        }

        return $formattedNumber;
    }

    /**
     * @return bool|string
     */
    public function getTomorrowsDate()
    {
        $dateTime = $this->dateTime->date($this->getCurrentDate());
        return date('Y-m-d ' . $dateTime->format('H:i:s'), strtotime('tomorrow'));
    }

    /**
     * @param $startDate
     *
     * @return string
     */
    public function getEndDate($startDate)
    {
        /**
         * Minus 1 as we only want the days in between.
         */
        $maximumNumberOfDeliveryDays = $this->shippingOptions->getMaxAmountOfDeliverydays() - 1;

        $endDate = $this->dateTime->date($startDate, 'nl_NL');
        // @codingStandardsIgnoreLine
        $endDate->add(new \DateInterval("P{$maximumNumberOfDeliveryDays}D"));

        return $endDate->format('d-m-Y');
    }

    /**
     * @param string $country
     *
     * @return array
     */
    public function getAllowedDeliveryOptions($country = 'NL')
    {
        $showPackageMachines = $this->shippingOptions->isPackageMachineFilterActive();
        $deliveryOptions     = [];

        if ($this->shippingOptions->isPakjegemakActive($country) && $showPackageMachines) {
            $deliveryOptions [] = self::PACKAGEBOX_DELIVERY_OPTION;
        }
        if ($country === 'NL' && $this->shippingOptions->isPakjegemakActive($country) && !$showPackageMachines) {
            $deliveryOptions [] = self::DISABLE_PACKAGEBOX_DELIVERY_OPTION;
        }

        return $deliveryOptions;
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

    /**
     * @return int|null
     */
    private function getCurrentDate()
    {
        if ($this->currentDate === null) {
            $this->currentDate = time();
        }

        return $this->currentDate;
    }
}
