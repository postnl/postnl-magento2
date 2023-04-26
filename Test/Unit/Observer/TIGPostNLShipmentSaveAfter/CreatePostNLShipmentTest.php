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
namespace TIG\PostNL\Test\Unit\Observer\TIGPostNLShipmentSaveAfter;

use PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount;
use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter\CreatePostNLShipment;
use TIG\PostNL\Test\TestCase;

class CreatePostNLShipmentTest extends TestCase
{
    protected $instanceClass = CreatePostNLShipment::class;

    /**
     * @return array
     */
    public function getOrderProvider()
    {
        return [
            'no order and no shipment id' => [
                null,
                null,
                1
            ],
            'no order and has shipment id' => [
                null,
                3,
                1
            ],
            'has matching ids' => [
                4,
                4,
                0
            ],
            'non matching ids' => [
                5,
                6,
                1
            ],
        ];
    }

    /**
     * @param $orderId
     * @param $shipmentId
     * @param $expectedCalls
     *
     * @dataProvider getOrderProvider
     */
    public function testGetOrder($orderId, $shipmentId, $expectedCalls)
    {
        $invokedAtMost = new InvokedAtMostCount(1);

        $orderMock = $this->getFakeMock(Order::class)->setMethods(['getOrderId'])->getMock();
        $orderMock->expects($invokedAtMost)->method('getOrderId')->willReturn($orderId);

        $orderRepositoryMock = $this->getFakeMock(OrderRepository::class)
            ->setMethods(['getByFieldWithValue'])
            ->getMock();
        $orderRepositoryMock->expects($this->exactly($expectedCalls))
            ->method('getByFieldWithValue')
            ->willReturn($orderMock);

        $instance = $this->getInstance(['orderRepository' => $orderRepositoryMock]);
        $this->setProperty('shipmentOrderId', $shipmentId, $instance);

        if ($orderId !== null) {
            $this->setProperty('order', $orderMock, $instance);
        }

        $result = $this->invoke('getOrder', $instance);
        $this->assertEquals($result, $orderMock);
    }
}
