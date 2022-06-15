<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Service\Carrier;

use Magento\Checkout\Model\Session\Proxy as Session;
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
            ->setMethods(['getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getRegionId', 'getFreeShipping'])
            ->getMock();
        $addressMock->expects($this->once())->method('getStreetFull')->willReturn('Kabelweg 37');
        $addressMock->expects($this->once())->method('getPostcode')->willReturn('1014 BA');
        $addressMock->expects($this->once())->method('getCity')->willReturn('Amsterdam');
        $addressMock->expects($this->once())->method('getCountryId')->willReturn('NL');
        $addressMock->expects($this->once())->method('getRegionId')->willReturn(null);
        $addressMock->expects($this->once())->method('getFreeShipping')->willReturn('1');

        $quoteMock = $this->getFakeMock(Quote::class)
            ->setMethods(['getStore', 'getShippingAddress', 'getAllItems', 'getSubtotal'])
            ->getMock();
        $quoteMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $quoteMock->expects($this->once())->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->expects($this->exactly(3))->method('getAllItems')->willReturn([]);
        $quoteMock->expects($this->once())->method('getSubtotal')->willReturn(10);

        $checkoutSessionMock = $this->getFakeMock(Session::class)->setMethods(['getQuote'])->getMock();
        $checkoutSessionMock->expects($this->once())->method('getQuote')->willReturn($quoteMock);

        $rateRequestMock = $this->getFakeMock(RateRequest::class)->setMethods(null)->getMock();

        $rateRequestFactoryMock = $this->getFakeMock(RateRequestFactory::class)->setMethods(['create'])->getMock();
        $rateRequestFactoryMock->expects($this->once())->method('create')->willReturn($rateRequestMock);

        $instance = $this->getInstance([
            'session' => $checkoutSessionMock,
            'rateRequestFactory' => $rateRequestFactoryMock
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
