<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Unit\Controller\Adminhtml\Order;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use TIG\PostNL\Controller\Adminhtml\Order\CreateShipments;
use TIG\PostNL\Test\TestCase;

class CreateShipmentTest extends TestCase
{
    public function getInstance($di = [])
    {
        return $this->objectManager->getObject(CreateShipments::class, $di);
    }

    public function orderHasShipmentProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
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

        $collectionMock = $this->getFakeMock(Collection::class);
        $shipmentsCollection = $collectionMock->getMock();
        $getSize = $shipmentsCollection->expects($this->once());
        $getSize->method('getSize');
        $getSize->willReturn($hasShipment ? 1 : 0);

        $getShipmentsCollection = $order->expects($this->once());
        $getShipmentsCollection->method('getShipmentsCollection');
        $getShipmentsCollection->willReturn($shipmentsCollection);

        $result = $this->invokeArgs('orderHasShipment', [$order], $instance);

        $this->assertEquals($expected, $result);
    }
}