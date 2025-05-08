<?php

namespace TIG\PostNL\Service\Timeframe\Filters;

use DateTimeInterface;

interface DaysSkipInterface
{
    /**
     * @param DateTimeInterface $day
     *
     * @return DateTimeInterface
     */
    public function skip(DateTimeInterface $day): DateTimeInterface;
}
