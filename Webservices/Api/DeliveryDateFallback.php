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
namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Service\Timeframe\IsPastCutOff;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\Data;

class DeliveryDateFallback
{
    private $isPastCutOff;

    private $timeZone;

    private $webshop;

    private $helper;

    public function __construct(
        IsPastCutOff $isPastCutOff,
        TimezoneInterface $timezone,
        Webshop $webshop,
        Data $data
    ) {
        $this->isPastCutOff = $isPastCutOff;
        $this->timeZone = $timezone;
        $this->webshop = $webshop;
        $this->helper = $data;
    }

    /**
     * Fallback for getting a deliveryday when order->getDeliveryday() returns null.
     */
    public function get()
    {
        $shippingDays = explode(',', $this->webshop->getShipmentDays());
        $nextDay      = '+1 day';
        if ($this->isPastCutOff->calculate()) {
            $nextDay = '+2 day';
        }

        $date = $this->getDate($nextDay);
        $day  = $this->helper->getDayOrWeekNumber($nextDay);
        if ($day == 0 || $day == 7) {
            $nextDay = $date .'+1 day';
        }

        $day = $this->helper->getDayOrWeekNumber($nextDay);
        if ($day == 1 && !in_array(0, $shippingDays)) {
            $nextDay = $date. '+2 day';
        }

        return $this->getDate($nextDay);
    }

    /**
     * @param $nextDay
     * @return string
     */
    private function getDate($nextDay)
    {
        $date = $this->timeZone->date(strtotime($nextDay));
        return $date->format('d-m-Y');
    }
}
