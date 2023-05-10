<?php

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Service\Timeframe\Filters\Options\Today;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;

class CutOffTimes implements DaysFilterInterface
{
    /**
     * @var IsPastCutOff
     */
    private $isPastCutOffTime;

    /**
     * @var TimezoneInterface
     */
    private $dateLoader;

    /**
     * @var TimezoneInterface
     */
    private $todayTimezone;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param IsPastCutOff      $isPastCutOff
     * @param TimezoneInterface $dateLoader
     * @param TimezoneInterface $today
     * @param ShippingOptions   $shippingOptions
     */
    public function __construct(
        IsPastCutOff $isPastCutOff,
        TimezoneInterface $dateLoader,
        TimezoneInterface $today,
        ShippingOptions $shippingOptions
    ) {
        $this->todayTimezone    = $today;
        $this->dateLoader       = $dateLoader;
        $this->isPastCutOffTime = $isPastCutOff;
        $this->shippingOptions  = $shippingOptions;
    }

    /**
     * @param object|array $days
     *
     * @return array
     */
    public function filter($days)
    {
        if (!$this->isPastCutOffTime() || !$this->isNextDay($days[0])) {
            return $days;
        }

        $firstDayTimeframe                       = $days[0]->Timeframes->TimeframeTimeFrame;
        $firstDayTimeframe                       = array_filter($firstDayTimeframe, [$this, 'filterNextDeliveryDay']);
        $days[0]->Timeframes->TimeframeTimeFrame = $firstDayTimeframe;

        // If no timeframe options remain, the whole day can be removed.
        if (empty($firstDayTimeframe)) {
            array_shift($days);
        }

        return array_values($days);
    }

    /**
     * Certain timeframe options (e.g. Today delivery) have their own cutoff rules and filters,
     * so they have to be excluded from this cutoff filter.
     * All other timeframe options are filtered from the next delivery day as expected from the standard cutoff rules.
     *
     * @param $option
     *
     * @return bool
     */
    private function filterNextDeliveryDay($option)
    {
        $option = $option->Options;
        if (!isset($option->string[0])) {
            return false;
        }

        $result = false;

        foreach ($option->string as $string) {
            if ($string === Today::TIMEFRAME_OPTION_TODAY && $this->shippingOptions->isTodayDeliveryActive()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @param $day
     *
     * @return bool
     */
    private function isNextDay($day)
    {
        $dayDateTime = $this->dateLoader->date($day->Date, null, true);
        $diff = $dayDateTime->diff($this->today());

        if ($diff->days == 1) {
            return true;
        }

        return false;
    }

    /**
     * @return \DateTime
     */
    private function today()
    {
        static $today = null;

        if ($today) {
            return $today;
        }

        return $today = $this->todayTimezone->date(
            $this->todayTimezone->date()->format('d-m-Y'),
            null,
            true,
            false
        );
    }

    /**
     * @return bool
     */
    private function isPastCutOffTime()
    {
        static $isPastCutoff = null;

        if ($isPastCutoff) {
            return $isPastCutoff;
        }

        return $isPastCutoff = $this->isPastCutOffTime->calculate();
    }
}
