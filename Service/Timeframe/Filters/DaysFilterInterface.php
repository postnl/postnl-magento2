<?php

namespace TIG\PostNL\Service\Timeframe\Filters;

interface DaysFilterInterface
{
    /**
     * @param object|array $days
     *
     * @return array
     */
    public function filter($days);
}
