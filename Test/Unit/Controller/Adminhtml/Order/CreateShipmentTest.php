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
namespace TIG\PostNL\Unit\Controller\Adminhtml\Order;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use TIG\PostNL\Controller\Adminhtml\Order\CreateShipments;
use TIG\PostNL\Test\TestCase;

class CreateShipmentTest extends TestCase
{
    protected $instanceClass = CreateShipments::class;

    public function orderHasShipmentProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    public function orderHasShipment(\PHPUnit_Framework_MockObject_MockObject $order, $hasShipment)
    {
        $collectionMock = $this->getFakeMock(Collection::class);
        $shipmentsCollection = $collectionMock->getMock();

        $getSize = $shipmentsCollection->expects($this->once());
        $getSize->method('getSize');
        $getSize->willReturn($hasShipment ? 1 : 0);

        $getShipmentsCollection = $order->expects($this->once());
        $getShipmentsCollection->method('getShipmentsCollection');
        $getShipmentsCollection->willReturn($shipmentsCollection);
    }

    public function setCurrentOrder(\PHPUnit_Framework_MockObject_MockObject $order, CreateShipments $instance)
    {
        $this->setProperty('currentOrder', $order, $instance);
    }

    /**
     * @param $hasShipment
     * @param $expected
     *
     * @dataProvider orderHasShipmentProvider
     */
    public function testOrderHasShipment($hasShipment, $expected)
    {
        $instance = $this->getInstance();
        $orderMock = $this->getFakeMock(Order::class);
        $order = $orderMock->getMock();
        $this->orderHasShipment($order, $hasShipment);

        $this->setCurrentOrder($order, $instance);
        $result = $this->invoke('orderHasShipment', $instance);

        $this->assertEquals($expected, $result);
    }

    public function orderIsValidProvider()
    {
        return [
            [false, false, false],
            [true, false, false],
            [false, true, true],
            [true, true, false],
        ];
    }

    /**
     * @param $hasShipment
     * @param $canShip
     * @param $expected
     *
     * @dataProvider orderIsValidProvider
     */
    public function testIsValidOrder($hasShipment, $canShip, $expected)
    {
        $instance = $this->getInstance();
        $orderMock = $this->getFakeMock(Order::class);
        $orderMock->setMethods(array('canShip', 'getShipmentsCollection'));
        $order = $orderMock->getMock();
        $this->orderHasShipment($order, $hasShipment);
        $this->setCurrentOrder($order, $instance);

        $canShipExpects = $order->expects($this->any());
        $canShipExpects->method('canShip');
        $canShipExpects->willReturn($canShip);

        $result = $this->invoke('isValidOrder', $instance);

        $this->assertEquals($expected, $result);
    }
}
