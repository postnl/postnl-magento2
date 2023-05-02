<?php

namespace TIG\PostNL\Unit\Model\Carrier;

use TIG\PostNL\Model\Carrier\Tablerate;
use TIG\PostNL\Test\TestCase;

class TablerateTest extends TestCase
{
    protected $instanceClass = Tablerate::class;

    public function testInstance()
    {
        $instance = $this->getInstance();
        $codeProperty = $this->getProperty('_code', $instance);

        $this->assertInstanceOf(Tablerate::class, $instance);
        $this->assertEquals('tig_postnl', $codeProperty);
    }
}
