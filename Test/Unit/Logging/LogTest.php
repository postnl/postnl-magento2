<?php

namespace TIG\PostNL\Test\Unit\Logging;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Test\Unit\Support\LoggingConfiguration;

class LogTest extends TestCase
{
    public function testConfiguredLevelsReachHandlers()
    {
        $handler = new TestHandler();
        $logger = new Log('postnl', new LoggingConfiguration(), [$handler]);

        $logger->debug('Request sent', ['request' => 42]);
        $logger->info('Disabled level');
        $this->assertTrue($logger->addRecord(Logger::ERROR, 'Request failed'));

        $this->assertCount(2, $handler->getRecords());
        $this->assertTrue($handler->hasDebugRecords());
        $this->assertTrue($handler->hasErrorRecords());
        $this->assertFalse($handler->hasInfoRecords());
        $this->assertSame(['request' => 42], $handler->getRecords()[0]['context']);
        $this->assertSame('postnl', $handler->getRecords()[0]['channel']);
    }

    public function testDisabledLevelReturnsFalse()
    {
        $handler = new TestHandler();
        $logger = new Log('postnl', new LoggingConfiguration(), [$handler]);

        $this->assertFalse($logger->addRecord(Logger::INFO, 'Disabled level'));
        $this->assertCount(0, $handler->getRecords());
    }
}
