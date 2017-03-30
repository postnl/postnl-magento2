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
namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\Label\Prepare;
use TIG\PostNL\Service\Shipment\Label\Type\TypeInterface;
use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Test\TestCase;

class PrepareTest extends TestCase
{
    public $instanceClass = Prepare::class;

    /**
     * @expectedException \TIG\PostNL\Exception
     * @expectedExceptionMessage test is not an instance of TIG\PostNL\Service\Shipment\Label\Type\TypeInterface
     */
    public function testRequiresTheRightInterface()
    {
        $typeConverter = $this->getObject(Type::class);

        $this->getInstance([
            'typeConverter' => $typeConverter,
            'types' => [
                'domestic' => $this->getMock(TypeInterface::class),
                'test' => 'randomstring',
            ]
        ]);
    }

    public function testCallsTheRightProcessor()
    {
        $typeConverter = $this->getObject(Type::class);

        $labelMock = $this->getLabelMock('EPS');

        $typeMock = $this->getMock(TypeInterface::class);

        $processMock = $typeMock->expects($this->once());
        $processMock->method('process');
        $processMock->with($labelMock);
        $processMock->willReturn('the new label');

        /** @var Prepare $instance */
        $instance = $this->getInstance([
            'typeConverter' => $typeConverter,
            'types' => [
                'domestic' => $this->getMock(TypeInterface::class),
                'eps' => $typeMock
            ]
        ]);

        $this->assertEquals('the new label', $instance->label($labelMock));
    }

    public function testDefaultsToDomestic()
    {
        $typeConverter = $this->getObject(Type::class);

        $labelMock = $this->getLabelMock('Evening');

        $typeMock = $this->getMock(TypeInterface::class);

        $processMock = $typeMock->expects($this->once());
        $processMock->method('process');
        $processMock->with($labelMock);
        $processMock->willReturn('the new label');

        /** @var Prepare $instance */
        $instance = $this->getInstance([
            'typeConverter' => $typeConverter,
            'types' => [
                'domestic' => $typeMock
            ]
        ]);

        $this->assertEquals('the new label', $instance->label($labelMock));
    }

    /**
     * @param $type
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getLabelMock($type)
    {
        $shipmentMock    = $this->getMock(ShipmentInterface::class);
        $getShipmentType = $shipmentMock->method('getShipmentType');
        $getShipmentType->willReturn($type);

        $labelMock   = $this->getMock(ShipmentLabelInterface::class);
        $getShipment = $labelMock->method('getShipment');
        $getShipment->willReturn($shipmentMock);

        return $labelMock;
    }
}
