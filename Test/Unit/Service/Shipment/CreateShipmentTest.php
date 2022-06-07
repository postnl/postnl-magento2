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
namespace TIG\PostNL\Test\Unit\Service\Shipment;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use TIG\PostNL\Service\Shipment\CreateShipment;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Helper\Data as Helper;

class CreateShipmentTest extends TestCase
{
    public $instanceClass = CreateShipment::class;

    public function testGetErrors()
    {
        $instance = $this->getInstance();
        $this->setProperty('errors', ['some errors', 'also an error'], $instance);

        $result = $instance->getErrors();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /**
     * @return array
     */
    public function isValidOrderProvider()
    {
        return [
            'has shipments' => [
                1,
                true,
                'tig_postnl_regular',
                false
            ],
            'cannot ship' => [
                0,
                false,
                'tig_postnl_regular',
                false
            ],
            'wrong shipment method' => [
                0,
                true,
                'free_shipping',
                false
            ],
            'is valid' => [
                0,
                true,
                'tig_postnl_regular',
                true
            ],
        ];
    }

    /**
     * @param $size
     * @param $canShip
     * @param $shippingMethod
     * @param $expected
     *
     * @dataProvider isValidOrderProvider
     */
    public function testIsValidOrder($size, $canShip, $shippingMethod, $expected)
    {
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSize'])
            ->getMock();
        $collectionMock->expects($this->once())->method('getSize')->willReturn($size);

        $mockHelper = $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isPostNLOrder'])
            ->getMock();
        $isPostNLShipment = $shippingMethod == 'tig_postnl_regular';
        $mockHelper->expects($this->any())->method('isPostNLOrder')->willReturn($isPostNLShipment);

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShipmentsCollection', 'canShip', 'getShippingMethod'])
            ->getMock();
        $orderMock->expects($this->once())->method('getShipmentsCollection')->willReturn($collectionMock);
        $orderMock->method('canShip')->willReturn($canShip);
        $orderMock->method('getShippingMethod')->willReturn($shippingMethod);

        $instance = $this->getInstance([
            'postnlHelper' => $mockHelper
        ]);
        $this->setProperty('currentOrder', $orderMock, $instance);

        $result = $this->invoke('isValidOrder', $instance);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function orderHasShipmentProvider()
    {
        return [
            'no shipment' => [0, false],
            'single shipment' => [1, true],
            'multiple shipments' => [4, true]
        ];
    }

    /**
     * @param $size
     * @param $expected
     *
     * @dataProvider orderHasShipmentProvider
     */
    public function testOrderHasShipment($size, $expected)
    {
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSize'])
            ->getMock();
        $collectionMock->expects($this->once())->method('getSize')->willReturn($size);

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShipmentsCollection'])
            ->getMock();
        $orderMock->expects($this->once())->method('getShipmentsCollection')->willReturn($collectionMock);

        $instance = $this->getInstance();
        $this->setProperty('currentOrder', $orderMock, $instance);

        $result = $this->invoke('orderHasShipment', $instance);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function handleExceptionForPossibleSoapErrorsProvider()
    {
        return [
            'no errors, no message' => [
                null,
                null,
                ''
            ],
            'no errors, has message' => [
                null,
                'exception message',
                'exception message'
            ],
            'string errors, no message' => [
                'error by string',
                null,
                ''
            ],
            'string errors, has message' => [
                'error by string',
                'exception message',
                'exception message'
            ],
            'array errors, no message' => [
                ['first error by array.', 'another error.'],
                null,
                '[POSTNL-0010] - An error occurred while processing this action. first error by array. another error.'
            ],
            'array errors, has message' => [
                ['first error by array.', 'another error.'],
                'exception message',
                '[POSTNL-0010] - An error occurred while processing this action. first error by array. another error.'
            ],
        ];
    }

    /**
     * @param $errors
     * @param $message
     * @param $expected
     *
     * @dataProvider handleExceptionForPossibleSoapErrorsProvider
     */
    public function testHandleExceptionForPossibleSoapErrors($errors, $message, $expected)
    {
        $exceptionMockBuilder = $this->getMockBuilder(\Exception::class)->setConstructorArgs([(string)$message]);

        if (null !== $errors) {
            $exceptionMockBuilder->setMethods(['getErrors']);
        }

        $exceptionMock = $exceptionMockBuilder->getMock();

        if (null !== $errors) {
            $exceptionMock->method('getErrors')->willReturn($errors);
        }

        $instance = $this->getInstance();
        $result = $this->invokeArgs('handleExceptionForPossibleSoapErrors', [$exceptionMock], $instance);
        $this->assertEquals($expected, $result);
    }
}
