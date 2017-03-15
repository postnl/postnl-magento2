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
namespace TIG\PostNL\Test\Unit\Helper\Labelling;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Helper\Labelling\GetLabels;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class GetLabelsTest extends TestCase
{
    protected $instanceClass = GetLabels::class;

    public function getProvider()
    {
        return [
            'no_shipments' => [
                null,
                [],
                0
            ],
            'success' => [
                3,
                (Object)['ResponseShipments' => (Object)['ResponseShipment' => [
                    0 => (Object)['Labels' => (Object)['Label' => [
                        0 => (Object)['Content' => 'Successful call']
                    ]]]
                ]]],
                1
            ],
        ];
    }

    /**
     * @param $shipmentIds
     * @param $callResponses
     * @param $expectedLabelCount
     *
     * @dataProvider getProvider
     */
    public function testGet($shipmentIds, $callResponses, $expectedLabelCount)
    {
        $shipmentRepositoryMock = $this->getShipmentRepositoryMock($shipmentIds);

        $labellingMock = $this->getFakeMock(Labelling::class);
        $labellingMock->setMethods(['setParameters', 'call']);
        $labellingMock = $labellingMock->getMock();

        $setParametersExpects = $labellingMock->expects($this->exactly(count($shipmentIds)));
        $setParametersExpects->method('setParameters');

        $callExpects = $labellingMock->expects($this->exactly(count($shipmentIds)));
        $callExpects->method('call');
        $callExpects->willReturn($callResponses);

        $validatorMock = $this->getMock(Validator::class);
        $validateMethod = $validatorMock->method('validate');
        $validateMethod->willReturnCallback(function ($input) {
            return $input;
        });

        /** @var GetLabels $instance */
        $instance = $this->getInstance([
            'labelling' => $labellingMock,
            'shipmentRepository' => $shipmentRepositoryMock,
            'labelValidator' => $validatorMock,
        ]);
        $result = $instance->get($shipmentIds);

        $this->assertCount($expectedLabelCount, $result);
    }

    /**
     * @param $id
     *
     * @return array
     */
    private function getShipmentMocks($id)
    {
        $mock = $this->getMock(ShipmentInterface::class);
        $this->mockFunction($mock, 'getParcelCount', 1);

        return $mock;
    }

    private function getShipmentRepositoryMock($shipmentIds)
    {
        $shipmentMocks = null;
        if ($shipmentIds) {
            $shipmentMocks = $this->getShipmentMocks($shipmentIds);
        }

        $mock = $this->getMock(ShipmentRepositoryInterface::class);
        $this->mockFunction($mock, 'getByFieldWithValue', $shipmentMocks);

        return $mock;
    }
}
