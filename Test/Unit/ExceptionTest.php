<?php

namespace TIG\PostNL\Test\Unit;

use TIG\PostNL\Test\TestCase;

class ExceptionTest extends TestCase
{
    public function testShowsTheMessage()
    {
        $exception = new \TIG\PostNL\Exception('This is my test message');

        $this->assertEquals('This is my test message', $exception->getMessage());
    }

    public function testAcceptsAnPhrase()
    {
        $exception = new \TIG\PostNL\Exception(__('This is my test message'));

        $this->assertEquals('This is my test message', $exception->getMessage());
    }

    public function testAddTheCodeIfSupplied()
    {
        $exception = new \TIG\PostNL\Exception('This is my test message', -999);

        $this->assertEquals('[-999] This is my test message', $exception->getMessage());
    }
}
