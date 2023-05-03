<?php

namespace TIG\PostNL\Test\Unit\Webservices\Parser\Label;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Parser\Label\Customer;

class CustomerTest extends TestCase
{
    protected $instanceClass = Customer::class;

    public function testGet()
    {
        $result = $this->getInstance()->get();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('CollectionLocation', $result);
        $this->assertArrayHasKey('Address', $result);
    }
}
