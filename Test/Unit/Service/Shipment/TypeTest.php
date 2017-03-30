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

use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Test\TestCase;

class TypeTest extends TestCase
{
    public $instanceClass = Type::class;

    public function returnsTheRightTypeProvider()
    {
        return [
            ['PG', 'NL', 'PG'],
            ['PGE', 'NL', 'PGE'],
            ['Evening', 'NL', 'Evening'],
            ['Daytime', 'NL', 'Daytime'],
            [null, 'NL', 'Daytime'],
            [null, 'DE', 'EPS'],
            [null, 'BE', 'EPS'],
            [null, 'US', 'GLOBALPACK'],
        ];
    }

    /**
     * @dataProvider returnsTheRightTypeProvider
     *
     * @param $type
     * @param $countryId
     * @param $expected
     *
     * @throws \Exception
     */
    public function testReturnsTheRightType($type, $countryId, $expected)
    {
        $shipmentMock = $this->getFakeMock(\Magento\Sales\Model\Order\Shipment::class, true);
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($countryId);

        $shippingAdddressExpects = $shipmentMock->expects($this->any());
        $shippingAdddressExpects->method('getShippingAddress');
        $shippingAdddressExpects->willReturn($address);

        $shipmentInterfaceMock = $this->getMock(\TIG\PostNL\Api\Data\ShipmentInterface::class);

        $getTypeExpects = $shipmentInterfaceMock->expects($this->once());
        $getTypeExpects->method('getShipmentType');
        $getTypeExpects->willReturn($type);

        $getShipmentMethod = $shipmentInterfaceMock->method('getShipment');
        $getShipmentMethod->willReturn($shipmentMock);

        /** @var Type $instance */
        $instance = $this->getInstance();

        $this->assertEquals($expected, $instance->get($shipmentInterfaceMock));
    }
}
