<?php

namespace TIG\PostNL\Service\Test\Integration\Order;

use TIG\PostNL\Service\Order\ShipAt;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Test\Integration\TestCase;

class ShipAtTest extends TestCase
{
    public $instanceClass = ShipAt::class;

    public function testShouldSetNullWhenNoAddressIsFound()
    {
        $quote = $this->getMock(QuoteInterface::class);
        $getShippingAddress = $quote->expects($this->once());
        $getShippingAddress->method('getShippingAddress');
        $getShippingAddress->willReturn(null);

        $order = $this->getOrder();
        $this->getInstance(['quote' => $quote])->set($order);

        $this->assertNull($order->getShipAt());
    }

    public function testCallsTheEndpint()
    {
        $endpoint = $this->getFakeMock(\TIG\PostNL\Webservices\Endpoints\SentDate::class, true);
        $call = $endpoint->method('call');
        $call->willReturn('2016-11-19');

        $address = $this->getObject(\Magento\Quote\Model\Quote\Address::class);

        $quote = $this->getMock(QuoteInterface::class);
        $getShippingAddress = $quote->expects($this->once());
        $getShippingAddress->method('getShippingAddress');
        $getShippingAddress->willReturn($address);

        $order = $this->getOrder();
        $this->getInstance(['quote' => $quote, 'endpoint' => $endpoint])->set($order);

        $this->assertEquals('2016-11-19', $order->getShipAt());
    }

    /**
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    private function getOrder()
    {
        $factory = $this->getObject(\TIG\PostNL\Model\OrderFactory::class);

        return $factory->create();
    }
}
