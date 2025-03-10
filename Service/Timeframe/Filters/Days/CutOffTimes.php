<?php

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
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
     * @param IsPastCutOff      $isPastCutOff
     * @param TimezoneInterface $dateLoader
     * @param TimezoneInterface $today
     */
    public function __construct(
        IsPastCutOff $isPastCutOff,
        TimezoneInterface $dateLoader,
        TimezoneInterface $today
    ) {
        $this->todayTimezone    = $today;
        $this->dateLoader       = $dateLoader;
        $this->isPastCutOffTime = $isPastCutOff;
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
        $days[0]->Timeframes->TimeframeTimeFrame = $firstDayTimeframe;

        // If no timeframe options remain, the whole day can be removed.
        if (empty($firstDayTimeframe)) {
            array_shift($days);
        }

        return array_values($days);
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
