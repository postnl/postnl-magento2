<?php

namespace TIG\PostNL\Test\Unit\Support;

class LoggingConfiguration extends \TIG\PostNL\Config\Provider\LoggingConfiguration
{
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getLoggingTypes()
    {
        return '100,400';
    }
}
