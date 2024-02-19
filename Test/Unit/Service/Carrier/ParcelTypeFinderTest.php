<?php

namespace TIG\PostNL\Test\Unit\Service\Carrier;

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Test\TestCase;

class ParcelTypeFinderTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Service\Carrier\ParcelTypeFinder::class;

    public function testFindsExtraAtHome()
    {
        $itemsToOption = $this->mockItemsToOption(ProductInfo::OPTION_EXTRAATHOME);

        $instance = $this->getInstance([
            'itemsToOption' => $itemsToOption,
        ]);

        $this->assertEquals(ProductInfo::OPTION_EXTRAATHOME, $instance->get());
    }

    public function testPakjegemakIsReturned()
    {
        $itemsToOption = $this->mockItemsToOption('');
        $orderRepositoryMock = $this->mockOrderRepository('PG');

        $instance = $this->getInstance([
            'itemsToOption' => $itemsToOption,
            'orderRepository' => $orderRepositoryMock,
        ]);

        $this->assertEquals('pakjegemak', $instance->get());
    }

    public function testRegularIsReturnedByDefault()
    {
        $itemsToOption = $this->mockItemsToOption('');
        $orderRepositoryMock = $this->mockOrderRepository('');

        $instance = $this->getInstance([
            'itemsToOption' => $itemsToOption,
            'orderRepository' => $orderRepositoryMock,
        ]);

        $this->assertEquals('regular', $instance->get());
    }

    public function testReturnsRegularWhenNoDatabaseRecordAvailable()
    {
        $orderRepositoryMock = $this->getFakeMock(OrderRepositoryInterface::class, true);
        $getByQuote = $orderRepositoryMock->method('getByQuoteId');
        $getByQuote->willReturn(null);

        $itemsToOption = $this->mockItemsToOption('');

        $instance = $this->getInstance([
            'itemsToOption' => $itemsToOption,
            'orderRepository' => $orderRepositoryMock,
        ]);

        $this->assertEquals('regular', $instance->get());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockItemsToOption($response)
    {
        $mock = $this->getFakeMock(\TIG\PostNL\Service\Options\ItemsToOption::class, true);
        $getFromQuote  = $mock->method('getFromQuote');
        $getFromQuote->willReturn($response);

        return $mock;
    }

    /**
     * @param $type
     *
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockOrderRepository($type)
    {
        $orderMock = $this->getFakeMock(OrderInterface::class, true);
        $getType = $orderMock->method('getType');
        $getType->willReturn($type);

        $orderRepository = $this->getFakeMock(OrderRepositoryInterface::class, true);
        $getByQuote = $orderRepository->method('getByQuoteId');
        $getByQuote->willReturn($orderMock);

        return $orderRepository;
    }
}
