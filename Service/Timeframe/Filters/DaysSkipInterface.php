<?php

namespace TIG\PostNL\Service\Timeframe\Filters;

use DateTimeInterface;

interface DaysSkipInterface
{
    /**
     * @param DateTimeInterface $day
     *
     * @return bool
     */
    public function skip(DateTimeInterface $day): bool;
}
