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

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Order\ProductCodeAndType;
use TIG\PostNL\Test\TestCase;

class ParcelTypeFinderTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Service\Carrier\ParcelTypeFinder::class;

    public function testFindsExtraAtHome()
    {
        $itemsToOption = $this->mockItemsToOption(ProductCodeAndType::OPTION_EXTRAATHOME);

        $instance = $this->getInstance([
            'itemsToOption' => $itemsToOption,
        ]);

        $this->assertEquals(ProductCodeAndType::OPTION_EXTRAATHOME, $instance->get());
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
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
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
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
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
