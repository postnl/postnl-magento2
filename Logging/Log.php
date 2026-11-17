<?php

namespace TIG\PostNL\Logging;

use Monolog\Logger;
use Psr\Log\AbstractLogger;
use TIG\PostNL\Config\Provider\LoggingConfiguration;

class Log extends AbstractLogger
{
    private LoggingConfiguration $logConfig;

    private string $name;

    private array $handlers;

    private array $processors;

    private ?Logger $logger = null;

    /**
     * @param string $name
     * @param LoggingConfiguration $loggingConfiguration
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        string $name,
        LoggingConfiguration $loggingConfiguration,
        array $handlers = [],
        array $processors = []
    ) {
        $this->name = $name;
        $this->logConfig = $loggingConfiguration;
        $this->handlers = $handlers;
        $this->processors = $processors;
    }

    /**
     * @param mixed $level
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $this->addRecord(Logger::toMonologLevel($level), $message, $context);
    }

    /**
     * @param mixed $level
     * @param string|\Stringable $message
     * @param array $context
     * @param \DateTimeImmutable|null $datetime
     * @return bool
     */
    public function addRecord($level, $message, array $context = [], $datetime = null): bool
    {
        if (!$this->logConfig->canLog($level)) {
            return false;
        }

        if ($this->logger === null) {
            $this->logger = new Logger($this->name, $this->handlers, $this->processors);
        }

        return $this->logger->addRecord($level, (string)$message, $context);
    }
}
