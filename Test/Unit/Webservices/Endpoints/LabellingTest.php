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
namespace TIG\PostNL\Test\Unit\Webservices\Endpoints;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Data;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class LabellingTest extends TestCase
{
    protected $instanceClass = Labelling::class;

    public function testSetParameters()
    {
        $shipmentMock = $this->getShipmentMock();

        $postnlOrderMock = $this->getFakeMock(\TIG\PostNL\Model\Order::class);
        $postnlOrderMock = $postnlOrderMock->getMock();

        $postnlShipmentMock = $this->getFakeMock(Shipment::class);
        $postnlShipmentMock->setMethods([
            'getShipment',
            'getPostNLOrder',
            'getTotalWeight',
            'getDeliveryDateFormatted',
            'isExtraCover',
        ]);
        $postnlShipmentMock = $postnlShipmentMock->getMock();
        $this->mockFunction($postnlShipmentMock, 'isExtraCover', false);

        $getShipmentExpects = $postnlShipmentMock->expects($this->atLeastOnce());
        $getShipmentExpects->method('getShipment');
        $getShipmentExpects->willReturn($shipmentMock);

        $getPostNLOrderExpects = $postnlShipmentMock->expects($this->atLeastOnce());
        $getPostNLOrderExpects->method('getPostNLOrder');
        $getPostNLOrderExpects->willReturn($postnlOrderMock);

        $shipmentDataMock = $this->getFakeMock(Data::class, true);
        $getExpects = $shipmentDataMock->expects($this->once());
        $getExpects->method('get');
        $getExpects->willReturn(['theShipmentData']);

        $instance = $this->getInstance([
            'shipmentData' => $shipmentDataMock,
        ]);
        $instance->setParameters($postnlShipmentMock);

        $requestParams = $this->getProperty('requestParams', $instance);

        $this->assertArrayHasKey('Message', $requestParams);
        $this->assertArrayHasKey('Customer', $requestParams);
        $this->assertArrayHasKey('Shipments', $requestParams);

        $requestParamsShipment = $requestParams['Shipments']['Shipment'];
        $this->assertInternalType('array', $requestParamsShipment);
        $this->assertEquals(['theShipmentData'], $requestParamsShipment);
    }

    public function getAddressDataProvider()
    {
        return [
            'normal' => [
                'street' => 'Kabelweg 37',
                'expected' => [
                    'Street' => 'Kabelweg',
                    'HouseNr' => '37',
                    'HouseNrExt' => '',
                ]
            ],
            'with ext' => [
                'street' => 'Kabelweg 37 a',
                'expected' => [
                    'Street' => 'Kabelweg',
                    'HouseNr' => '37',
                    'HouseNrExt' => 'a',
                ]
            ],
            'with empty address' => [
                'street' => '',
                'expected' => [
                    'Street' => '',
                    'HouseNr' => '',
                    'HouseNrExt' => '',
                ]
            ],
        ];
    }

    /**
     * @param $street
     * @param $expected
     *
     * @throws \Exception
     *
     * @dataProvider getAddressDataProvider
     */
    public function testGetAddressData($street, $expected)
    {
        /** @var Address $shippingAddress */
        $shippingAddress = $this->getObject(Address::class);
        $shippingAddress->setStreet($street);

        $instance = $this->getInstance();

        $result = $this->invokeArgs('getAddressData', [$shippingAddress], $instance);

        $this->assertEquals($expected['Street'], $result['Street']);
        $this->assertEquals($expected['HouseNr'], $result['HouseNr']);
        $this->assertEquals($expected['HouseNrExt'], $result['HouseNrExt']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getShipmentMock()
    {
        $addressMock = $this->getAddressMock();

        $orderMock = $this->getFakeMock(Order::class);
        $orderMock = $orderMock->getMock();

        $shipmentMock = $this->getFakeMock(\Magento\Sales\Model\Order\Shipment::class);
        $shipmentMock->setMethods(['getShippingAddress', 'getOrder']);
        $shipmentMock = $shipmentMock->getMock();

        $getOrderExpects = $shipmentMock->expects($this->once());
        $getOrderExpects->method('getOrder');
        $getOrderExpects->willReturn($orderMock);

        $getShippingAddressExpects = $shipmentMock->expects($this->atLeastOnce());
        $getShippingAddressExpects->method('getShippingAddress');
        $getShippingAddressExpects->willReturn($addressMock);

        return $shipmentMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAddressMock()
    {
        $addressMock = $this->getFakeMock(Address::class);
        $addressMock->setMethods(['getStreet']);
        $addressMock = $addressMock->getMock();

        $getStreetExpects = $addressMock->expects($this->once());
        $getStreetExpects->method('getStreet');
        $getStreetExpects->willReturn(['Kabelweg', '37']);

        return $addressMock;
    }
}
