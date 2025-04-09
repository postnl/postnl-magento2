<?php

namespace TIG\PostNL\Test\Unit\Webservices\Parser\Label;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Parser\Label\Customer;

class CustomerTest extends TestCase
{
    protected $instanceClass = Customer::class;

    public function testGet()
    {
        $shipment = $this->getFakeMock(ShipmentInterface::class)->getMock();
        $shipment->method('getIsSmartReturn')->willReturn(false);
        $result = $this->getInstance()->get($shipment);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('CollectionLocation', $result);
        $this->assertArrayHasKey('Address', $result);
    }
}
