<?php

namespace TIG\PostNL\Unit\Config\Source\General;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Source\General\Logging;
use Monolog\Logger as Monolog;

class LoggingTest extends TestCase
{
    protected $instanceClass = Logging::class;

    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertCount(8, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }

    public function testEqualToMonolog()
    {
        $postnlOptions  = $this->getProperty('levels');
        foreach ($postnlOptions as $level => $name) {
            $this->assertSame(Monolog::getLevelName($level), $name);
        }
    }
}
