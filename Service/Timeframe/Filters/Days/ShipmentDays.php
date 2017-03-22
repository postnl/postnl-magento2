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
use TIG\PostNL\Helper\Data;

/**
 * This filter compares the time frames dates to the shipment days of the webshop configuration.
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
     * @var Data
     */
    private $postNLHelper;

    /**
     * @var array
     */
    private $availableDays;

    /**
     * @param Webshop $webshop
     * @param ShippingOptions $shippingOptions
     * @param Data $data
     */
    public function __construct(
        Webshop $webshop,
        ShippingOptions $shippingOptions,
        Data $data
    ) {
        $this->webshopSettings = $webshop;
        $this->shippingOptions = $shippingOptions;
        $this->postNLHelper    = $data;

        $this->availableDays = $this->getDeliveryDatesArray();
    }

    /**
     * @param array|object $days
     *
     * @return array
     */
    public function filter($days)
    {
        $filteredDays = array_filter($days, [$this, 'isAvailableWithShipmentDays']);

        return array_values($filteredDays);
    }

    /**
     * Finds the first possible shipment day and match it with day number of given day.
     *
     * @param object $day
     *
     * @return bool
     */
    public function isAvailableWithShipmentDays($day)
    {
        $numberOfDay = $this->postNLHelper->getDayOrWeekNumber($day->Date);

        if (!in_array($numberOfDay, $this->availableDays)) {
            return false;
        }

        return true;
    }

    /**
     * This will format the shipment days to valid delivery dates.
     * +1 is the delay of PostNL.
     *
     * @return array
     */
    private function getDeliveryDatesArray()
    {
        $daysAvailable = [];
        $shipmentDays  = explode(',', $this->webshopSettings->getShipmentDays());
        $deliveryDelay = $this->shippingOptions->getDeliveryDelay();

        foreach ($shipmentDays as $shipDay) {
            $validDay = ($shipDay + $deliveryDelay) % 7;
            $daysAvailable[] = $validDay == '0' ? 7 : $validDay;
        }

        return $daysAvailable;
    }
}
