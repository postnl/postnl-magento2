<?php

namespace TIG\PostNL\Webservices\Api;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;
use function date;
use function explode;
use function in_array;
use function strtotime;

class DeliveryDateFallback
{
    private IsPastCutOff $isPastCutOff;

    private TimezoneInterface $timeZone;

    private Webshop $webshop;


    public function __construct(
        IsPastCutOff $isPastCutOff,
        TimezoneInterface $timezone,
        Webshop $webshop
    ) {
        $this->isPastCutOff = $isPastCutOff;
        $this->timeZone = $timezone;
        $this->webshop = $webshop;
    }

    /**
     * Fallback for getting a deliveryday when order->getDeliveryday() returns null.
     */
    public function get()
    {
        $shippingDays = explode(',', $this->webshop->getShipmentDays());
        $nextDay = '+1 day';
        if ($this->isPastCutOff->calculate()) {
            $nextDay = '+2 day';
        }

        $date = $this->getDate($nextDay);
        $shippingDaysCount = count($shippingDays);
        while (
            !in_array(
                $this->normaliseToConfigDayNumber(date('N', strtotime($date))),
                $shippingDays
            )
            && $shippingDaysCount
        ) {
            $date = $this->getDate($date . '+1 day');
        }

        return $this->getDate($date);
    }

    public function getDate(string $day): string
    {
        return $this->timeZone->date(strtotime($day))->format('d-m-Y');
    }

    /**
     * Convert date('N') output (1=Mon … 7=Sun) to the day-number format stored in
     * the shipment-days configuration, where Sunday is represented as '0'.
     */
    private function normaliseToConfigDayNumber(string $isoWeekday): string
    {
        if ($isoWeekday === '7') {
            return '0';
        }

        return $isoWeekday;
    }
}
