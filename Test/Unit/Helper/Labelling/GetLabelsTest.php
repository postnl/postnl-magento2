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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Helper\Labelling;

use TIG\PostNL\Helper\Labelling\GetLabels;
use TIG\PostNL\Model\ResourceModel\Shipment\Collection;
use TIG\PostNL\Model\ResourceModel\Shipment\CollectionFactory;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class GetLabelsTest extends TestCase
{
    protected $instanceClass = GetLabels::class;

    public function getProvider()
    {
        return [
            'no_shipments' => [
                [],
                [],
                0
            ],
            'all_fail' => [
                [1],
                [
                    'Failed call'
                ],
                0
            ],
            'all_success' => [
                [2, 3],
                [
                    (Object)['ResponseShipments' => (Object)['ResponseShipment' => [
                        0 => (Object)['Labels' => (Object)['Label' => [
                            0 => (Object)['Content' => 'Successful call']
                        ]]]
                    ]]],
                    (Object)['ResponseShipments' => (Object)['ResponseShipment' => [
                        0 => (Object)['Labels' => (Object)['Label' => [
                            0 => (Object)['Content' => 'Another successful call']
                        ]]]
                    ]]]
                ],
                2
            ],
            'mixed' => [
                [4, 5, 6],
                [
                    (Object)['ResponseShipments' => (Object)['ResponseShipment' => [
                        0 => (Object)['Labels' => (Object)['Label' => [
                            0 => (Object)['Content' => 'Successful call']
                        ]]]
                    ]]],
                    'Failed call',
                    (Object)['ResponseShipments' => (Object)['ResponseShipment' => [
                        0 => (Object)['Labels' => (Object)['Label' => [
                            0 => (Object)['Content' => 'Second successful call']
                        ]]]
                    ]]]
                ],
                2
            ]
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
        $collectionFactoryMock = $this->getCollectionFactoryMock($shipmentIds);

        $labellingMock = $this->getFakeMock(Labelling::class);
        $labellingMock->setMethods(['setParameters', 'call']);
        $labellingMock = $labellingMock->getMock();

        $setParametersExpects = $labellingMock->expects($this->exactly(count($shipmentIds)));
        $setParametersExpects->method('setParameters');

        $callExpects = $labellingMock->expects($this->exactly(count($shipmentIds)));
        $callExpects->method('call');
        $callExpects->will(new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($callResponses));

        $instance = $this->getInstance(['labelling' => $labellingMock, 'collectionFactory' => $collectionFactoryMock]);
        $result = $instance->get($shipmentIds);

        $this->assertCount($expectedLabelCount, $result);
    }

    /**
     * @param $shipmentIds
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCollectionFactoryMock($shipmentIds)
    {
        $shipmentMocks = $this->getShipmentMocks($shipmentIds);

        $collectionMock = $this->getFakeMock(Collection::class);
        $collectionMock->setMethods(['addFieldToFilter', 'count', 'getItems']);
        $collectionMock = $collectionMock->getMock();

        $countExpects = $collectionMock->expects($this->once());
        $countExpects->method('count');
        $countExpects->willReturn(count($shipmentIds));

        $getItemsExpect = $collectionMock->expects($this->any());
        $getItemsExpect->method('getItems');
        $getItemsExpect->willReturn($shipmentMocks);

        $collectionFactoryMock = $this->getFakeMock(CollectionFactory::class);
        $collectionFactoryMock->setMethods(['create']);
        $collectionFactoryMock = $collectionFactoryMock->getMock();

        $createExpects = $collectionFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($collectionMock);

        return $collectionFactoryMock;
    }

    /**
     * @param $shipmentIds
     *
     * @return array
     */
    private function getShipmentMocks($shipmentIds)
    {
        $shipmentMocks = [];

        foreach ($shipmentIds as $id) {
            $mock = $this->getFakeMock(Shipment::class);
            $mock->setMethods(['getEntityId']);
            $mock = $mock->getMock();

            $getEntityIdExpects = $mock->expects($this->once());
            $getEntityIdExpects->method('getEntityId');
            $getEntityIdExpects->willReturn($id);

            $shipmentMocks[] = $mock;
        }

        return $shipmentMocks;
    }
}
