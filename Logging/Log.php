<?php

namespace TIG\PostNL\Logging;

use Monolog\Logger;
use TIG\PostNL\Config\Provider\LoggingConfiguration;

class Log extends Logger
{
    /**
     * @var LoggingConfiguration
     */
    private $logConfig;

    /**
     * @param string $name
     * @param array $handlers
     * @param array $processors
     * @param LoggingConfiguration $loggingConfiguration
     */
    public function __construct(
        string $name,
        LoggingConfiguration $loggingConfiguration,
        array $handlers = [],
        array $processors = []
    ) {
        $this->logConfig = $loggingConfiguration;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     * @param null $datetime
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = [], $datetime = null): bool
    {
        if (!$this->logConfig->canLog($level)) {
            return false;
        }

        return parent::addRecord($level, $message, $context);
    }
}
