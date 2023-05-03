<?php

namespace TIG\PostNL\Unit\Model\Carrier;

use TIG\PostNL\Model\Carrier\PostNL;
use TIG\PostNL\Test\TestCase;

class PostNLTest extends TestCase
{
    protected $instanceClass = PostNL::class;

    public function testAllowedMethods()
    {
        $instance = $this->getInstance();
        $result = $instance->getAllowedMethods();

        $this->assertArrayHasKey('tig_postnl', $result);
        $this->assertEquals(['tig_postnl' => ''], $result);
    }
}
