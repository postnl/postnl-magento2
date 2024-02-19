<?php

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
        while (!in_array(date('N', strtotime($date)), $shippingDays) && count($shippingDays)) {
            $date = $this->getDate($date . '+1 day');
        }

        return $this->getDate($date);
    }

    /**
     * @param $day
     * @return string
     */
    public function getDate($day)
    {
        $date = $this->timeZone->date(strtotime($day));
        return $date->format('d-m-Y');
    }
}
