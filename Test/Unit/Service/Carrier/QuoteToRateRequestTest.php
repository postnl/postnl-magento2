<?php

namespace TIG\PostNL\Test\Unit\Service\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Store\Model\Store;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Test\TestCase;

class QuoteToRateRequestTest extends TestCase
{
    public $instanceClass = QuoteToRateRequest::class;

    public function testGet()
    {
        $storeMock = $this->getFakeMock(Store::class)->setMethods(['getWebsiteId'])->getMock();
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn(1);

        $addressMock = $this->getFakeMock(Address::class)
            ->setMethods(['getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getRegionId', 'getFreeShipping', 'getSubtotal'])
            ->getMock();
        $addressMock->expects($this->once())->method('getStreetFull')->willReturn('Kabelweg 37');
        $addressMock->expects($this->once())->method('getPostcode')->willReturn('1014 BA');
        $addressMock->expects($this->once())->method('getCity')->willReturn('Amsterdam');
        $addressMock->expects($this->once())->method('getCountryId')->willReturn('NL');
        $addressMock->expects($this->once())->method('getRegionId')->willReturn(null);
        $addressMock->expects($this->once())->method('getFreeShipping')->willReturn('1');
        $addressMock->method('getSubtotal')->willReturn(10);

        $quoteMock = $this->getFakeMock(Quote::class)
            ->setMethods(['getStore', 'getShippingAddress', 'getAllItems', 'getSubtotal'])
            ->getMock();
        $quoteMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->expects($this->exactly(3))->method('getAllItems')->willReturn([]);
        $quoteMock->method('getSubtotal')->willReturn(10);

        $checkoutSessionMock = $this->getFakeMock(Session::class)->setMethods(['getQuote'])->getMock();
        $checkoutSessionMock->expects($this->once())->method('getQuote')->willReturn($quoteMock);

        $scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class)->getMock();
        $scopeConfigMock->expects($this->once())->method('getValue')->willReturn(false);

        $rateRequestMock = $this->getFakeMock(RateRequest::class)->setMethods(null)->getMock();

        $rateRequestFactoryMock = $this->getFakeMock(RateRequestFactory::class)->setMethods(['create'])->getMock();
        $rateRequestFactoryMock->expects($this->once())->method('create')->willReturn($rateRequestMock);

        $instance = $this->getInstance([
            'session' => $checkoutSessionMock,
            'rateRequestFactory' => $rateRequestFactoryMock,
            'scopeConfig' => $scopeConfigMock
        ]);

        $result = $instance->get();
        $this->assertInstanceOf(RateRequest::class, $result);

        $this->assertEquals(1, $result->getWebsiteId());
        $this->assertEquals('Kabelweg 37', $result->getDestStreet());
        $this->assertEquals('1014 BA', $result->getDestPostcode());
        $this->assertEquals('Amsterdam', $result->getDestCity());
        $this->assertEquals('NL', $result->getDestCountryId());
        $this->assertEquals(0, $result->getPackageQty());
        $this->assertEquals(0, $result->getPackageWeight());
        $this->assertEquals(0, $result->getPackageValue());
        $this->assertNull($result->getDestRegionId());
        $this->assertEquals(10, $result->getOrderSubtotal());
        $this->assertTrue($result->getFreeShipping());
    }
}
