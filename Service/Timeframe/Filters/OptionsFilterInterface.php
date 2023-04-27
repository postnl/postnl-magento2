<?php

namespace TIG\PostNL\Service\Timeframe\Filters;

interface OptionsFilterInterface
{
    /**
     * @param object|array $options
     *
     * @return bool
     */
    public function filter($options);
}
