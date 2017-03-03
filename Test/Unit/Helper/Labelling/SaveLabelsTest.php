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

use TIG\PostNL\Helper\Labelling\SaveLabels;
use TIG\PostNL\Model\ResourceModel\Shipment\Collection;
use TIG\PostNL\Model\ResourceModel\Shipment\CollectionFactory;
use TIG\PostNL\Model\ResourceModel\ShipmentLabel\Collection as LabelCollection;
use TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory as LabelCollectionFactory;
use TIG\PostNL\Model\ShipmentLabel;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Test\TestCase;

class SaveLabelsTest extends TestCase
{
    protected $instanceClass = SaveLabels::class;

    public function saveProvider()
    {
        return [
            'single_shipment' => [
                [
                    1 => 'abc'
                ]
            ],
            'multiple_shipments' => [
                [
                    2 => 'def',
                    3 => 'ghi'
                ]
            ],
            'no_shipments' => [[]]
        ];
    }

    /**
     * @param $shipmentLabels
     *
     * @dataProvider saveProvider
     */
    public function testSave($shipmentLabels)
    {
        $collectionFactoryMock = $this->getCollectionFactoryMock();
        $labelCollectionFactoryMock = $this->getLabelCollectionFactoryMock();
        $shipmentLabelFactoryMock = $this->getShipmentLabelFactory($shipmentLabels);

        $instance = $this->getInstance([
            'shipmentCollectionFactory' => $collectionFactoryMock,
            'shipmentLabelCollectionFactory' => $labelCollectionFactoryMock,
            'shipmentLabelFactory' => $shipmentLabelFactoryMock
        ]);

        $result = $instance->save($shipmentLabels);

        $this->assertInternalType('array', $result);

        foreach ($result as $resultRow) {
            $this->assertInstanceOf(ShipmentLabel::class, $resultRow);
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCollectionFactoryMock()
    {
        $collectionMock = $this->getFakeMock(Collection::class);
        $collectionMock->setMethods(['addFieldToFilter', 'setDataToAll', 'save']);
        $collectionMock = $collectionMock->getMock();

        $addFieldToFilterExpects = $collectionMock->expects($this->once());
        $addFieldToFilterExpects->method('addFieldToFilter');

        $setDataToAllExpects = $collectionMock->expects($this->once());
        $setDataToAllExpects->method('setDataToAll');

        $saveExpects = $collectionMock->expects($this->once());
        $saveExpects->method('save');

        $collectionFactoryMock = $this->getFakeMock(CollectionFactory::class);
        $collectionFactoryMock->setMethods(['create']);
        $collectionFactoryMock = $collectionFactoryMock->getMock();

        $createExpects = $collectionFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($collectionMock);

        return $collectionFactoryMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getLabelCollectionFactoryMock()
    {
        $collectionMock = $this->getFakeMock(LabelCollection::class);
        $collectionMock->setMethods(['load', 'save']);
        $collectionMock = $collectionMock->getMock();

        $loadExpects = $collectionMock->expects($this->once());
        $loadExpects->method('load');

        $saveExpects = $collectionMock->expects($this->once());
        $saveExpects->method('save');

        $collectionFactoryMock = $this->getFakeMock(LabelCollectionFactory::class);
        $collectionFactoryMock->setMethods(['create']);
        $collectionFactoryMock = $collectionFactoryMock->getMock();

        $createExpects = $collectionFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($collectionMock);

        return $collectionFactoryMock;
    }

    /**
     * @param array $shipmentLabels
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getShipmentLabelFactory($shipmentLabels)
    {
        $shipmentLabelMock = $this->getFakeMock(ShipmentLabel::class);
        $shipmentLabelMock->setMethods(['setParentId', 'setLabel']);
        $shipmentLabelMock = $shipmentLabelMock->getMock();

        $setParentIdExpects = $shipmentLabelMock->expects($this->any());
        $setParentIdExpects->method('setParentId');
        $setParentIdExpects->withConsecutive($this->onConsecutiveCalls(array_keys($shipmentLabels)));

        $setLabelExpects = $shipmentLabelMock->expects($this->exactly(count($shipmentLabels)));
        $setLabelExpects->method('setLabel');
        $setLabelExpects->withConsecutive($this->onConsecutiveCalls(array_values($shipmentLabels)));

        $shipmentLabelFactoryMock = $this->getFakeMock(ShipmentLabelFactory::class);
        $shipmentLabelFactoryMock->setMethods(['create']);
        $shipmentLabelFactoryMock = $shipmentLabelFactoryMock->getMock();

        $createExpects = $shipmentLabelFactoryMock->expects($this->any());
        $createExpects->method('create');
        $createExpects->willReturn($shipmentLabelMock);

        return $shipmentLabelFactoryMock;
    }
}
