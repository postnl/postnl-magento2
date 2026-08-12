<?php
declare(strict_types=1);

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
    public function __construct(
        private readonly IsPastCutOff $isPastCutOff,
        private readonly TimezoneInterface $timeZone,
        private readonly Webshop $webshop,
    ) {
    }

    /**
     * First valid shipping day from today (or tomorrow if today is past cutoff / not a shipping day).
     * Used to set ship_at on the PostNL order/shipment when the PostNL API is unavailable.
     */
    public function getShipAtDate(): string
    {
        $shippingDays = explode(',', $this->webshop->getShipmentDays());
        $today = $this->timeZone->date();
        $todayDayNumber = $this->normaliseToConfigDayNumber($today->format('N'));

        // Today is a valid shipping day and we haven't missed the cutoff: ship today.
        if (in_array($todayDayNumber, $shippingDays) && !$this->isPastCutOff->calculate()) {
            return $today->format('d-m-Y');
        }

        $date = $this->getDate('+1 day');
        $i = 0;
        while (
            !in_array($this->normaliseToConfigDayNumber(date('N', strtotime($date))), $shippingDays)
            && $i < 7
        ) {
            $date = $this->getDate($date . '+1 day');
            $i++;
        }

        return $this->getDate($date);
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
        $i = 0;
        while (
            !in_array(
                $this->normaliseToConfigDayNumber(date('N', strtotime($date))),
                $shippingDays
            )
            && $i < 7
        ) {
            $date = $this->getDate($date . '+1 day');
            $i++;
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