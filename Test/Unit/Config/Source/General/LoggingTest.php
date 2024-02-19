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
        $monoLogOptions = $this->getProperty('levels', $this->getObject(Monolog::class, ['name' => 'PostNLTestLogger']));

        $this->assertEquals(
            $monoLogOptions, $postnlOptions,
            'PostNL log options are not the same as Monologs'
        );
    }
}
