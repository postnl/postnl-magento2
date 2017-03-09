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
namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Config\Provider\ShippingOptions;

/**
 * Class ShipmentDays
 * This filter compares the timeframes dates to the shipmentdays of the webshopconfiguration.
 *
 * @package TIG\PostNL\Service\Timeframe\Filters\Days
 */
class ShipmentDays implements DaysFilterInterface
{
    /**
     * @var Webshop
     */
    private $webshopSettings;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param Webshop $webshop
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        Webshop $webshop,
        ShippingOptions $shippingOptions
    ) {
        $this->webshopSettings = $webshop;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param array|object $days
     *
     * @return array
     */
    public function filter($days)
    {
        $filterdDays = array_filter($days, [$this, 'isAvailableWithShipmentdays']);
        return array_values($filterdDays);
    }

    /**
     * Finds the first posible shipment day and match it with day number of given day.
     *
     * @param object $value
     *
     * @return bool
     */
    public function isAvailableWithShipmentdays($value)
    {
        $numberOfDay   = date('w', strtotime($value->Date)) %7;
        $availableDays = $this->getDeliveryDatesArray();

        if ($numberOfDay < $availableDays[0] && $this->isSameWeek($value->Date)) {
            return false;
        }

        if ($numberOfDay == $availableDays[0] && $this->isSameWeek($value->Date) && $this->isPassedCutoffTime()) {
            return false;
        }

        if ($numberOfDay == '0') {
            return false; //Sunday delivery is not implemented yet.
        }

        return true;
    }

    /**
     * @param $date
     *
     * @return bool
     */
    private function isSameWeek($date)
    {
        $currentWeek = date('W', strtotime(date('Y-m-d')));
        $weekOfDate  = date('W', strtotime($date));

        return ($currentWeek == $weekOfDate);
    }

    /**
     * This will format the shipmentdays to valid deliverydates.
     * +1 is the delay of PostNL.
     *
     * @param array $daysAvailable
     *
     * @return array
     */
    private function getDeliveryDatesArray($daysAvailable = [])
    {
        $shipmentDays  = explode(',', $this->webshopSettings->getShipmentDays());
        $deliveryDelay = $this->shippingOptions->getDeliveryDelay();

        foreach ($shipmentDays as $shipday) {
            $validDay = ($shipday + $deliveryDelay) % 7;
            $daysAvailable[] = $validDay == '0' ? 1 : $validDay;
        }

        return $daysAvailable;
    }

    /**
     * When an order is placed after this time, another day will be added to the shipping duration
     *
     * @return bool
     */
    private function isPassedCutoffTime()
    {
        $cutOffTime  = $this->webshopSettings->getCutOffTime();
        $currentTime = date('H:i:s', time());

        return ($currentTime > $cutOffTime);
    }
}
