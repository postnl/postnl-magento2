<?php

namespace TIG\PostNL\Service\Timeframe;

use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Service\Timeframe\Filters\OptionsFilterInterface;

class Filter
{
    /**
     * @var DaysFilterInterface[] array
     */
    private $dayFilters;

    /**
     * @var OptionsFilterInterface[] array
     */
    private $optionsFilter;

    /**
     * @param array DaysFilterInterface[] $dayFilters
     * @param array OptionsFilterInterface[] $optionsFilters
     */
    public function __construct(
        $dayFilters = [],
        $optionsFilters = []
    ) {
        $this->dayFilters = $dayFilters;
        $this->optionsFilter = $optionsFilters;
    }

    /**
     * @param $days
     *
     * @return array
     */
    public function days($days)
    {
        /** @var DaysFilterInterface $filter */
        foreach ($this->dayFilters as $filter) {
            $days = $filter->filter($days);
        }

        return $days;
    }

    /**
     * @param $options
     *
     * @return object|array
     */
    public function options($options)
    {
        /** @var OptionsFilterInterface $filter */
        foreach ($this->optionsFilter as $filter) {
            $options = $filter->filter($options);
        }

        return $options;
    }
}
