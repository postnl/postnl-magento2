<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\LoggingConfiguration;

class LoggingConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = LoggingConfiguration::class;

    public function testGetLoggingTypes($value = '100,200,300')
    {
        $instance = $this->getInstance();
        $this->setXpath(LoggingConfiguration::XPATH_LOGGING_TYPE, $value);
        $this->assertEquals($value, $instance->getLoggingTypes());
    }

    public function canLogProvider()
    {
        return [
            'can log' => [100, '100,200,300', true],
            'can not log' => [400, '100,200,300', false]
        ];
    }

    /**
     * @dataProvider canLogProvider
     * @param $level
     * @param $value
     * @param $expected
     */
    public function testCanLog($level, $value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpath(LoggingConfiguration::XPATH_LOGGING_TYPE, $value);

        $result = $instance->canLog($level);
        $this->assertEquals($expected, $result);
    }
}
