<?php

namespace TIG\PostNL\Service\Test\Intergration\Order;

use TIG\PostNL\Service\Order\FirstDeliveryDate;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Test\Integration\TestCase;

class FirstDeliveryDateTest extends TestCase
{
    public $instanceClass = FirstDeliveryDate::class;

    public function testShouldReturnNullWhenTheQuoteHasNoAddress()
    {
        $quote = $this->getMock(QuoteInterface::class);
        $getShippingAddress = $quote->expects($this->once());
        $getShippingAddress->method('getShippingAddress');
        $getShippingAddress->willReturn(null);

        $order = $this->getOrder();
        $this->getInstance(['quote' => $quote])->set($order);

        $this->assertNull($order->getDeliveryDate());
    }

    public function testTheEndpointGetsCalled()
    {
        $endpoint = $this->getFakeMock(\TIG\PostNL\Webservices\Endpoints\DeliveryDate::class, true);
        $call = $endpoint->method('call');
        $call->willReturn((object)['DeliveryDate' => '2016-11-19']);

        $shippingConfig = $this->getFakeMock(\TIG\PostNL\Config\Provider\ShippingOptions::class, true);
        $isDeliverydaysActive = $shippingConfig->method('isDeliverydaysActive');
        $isDeliverydaysActive->willReturn(true);

        $address = $this->getObject(\Magento\Quote\Model\Quote\Address::class);
        $address->setCountryId('NL');

        $quote = $this->getMock(QuoteInterface::class);
        $getShippingAddress = $quote->expects($this->once());
        $getShippingAddress->method('getShippingAddress');
        $getShippingAddress->willReturn($address);

        $order = $this->getOrder();
        $this->getInstance(['quote' => $quote, 'endpoint' => $endpoint, 'shippingConfig' => $shippingConfig])->set($order);

        $this->assertEquals('2016-11-19', $order->getDeliveryDate());
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
