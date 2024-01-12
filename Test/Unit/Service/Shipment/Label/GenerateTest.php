<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Label\Generate;
use TIG\PostNL\Service\Shipment\Label\Merge;
use TIG\PostNL\Service\Shipment\Label\Prepare;
use TIG\PostNL\Service\Shipment\Label\Type\Domestic;
use TIG\PostNL\Test\TestCase;

class GenerateTest extends TestCase
{
    public $instanceClass = Generate::class;

    public function testThePrepareClassIsCalled()
    {
        $prepareMock = $this->getFakeMock(Prepare::class, true);
        $labelExpects = $prepareMock->expects($this->exactly(2));
        $labelExpects->method('label');

        $shipmentMock = $this->getFakeMock(Shipment::class, true);
        $shipmentTypeExpects = $shipmentMock->expects($this->exactly(2));
        $shipmentTypeExpects->method('getShipmentType');
        $shipmentTypeExpects->willReturn('Domestic');

        $labelMock = $this->getFakeMock(Domestic::class, true);

        $labelExpects->willReturn(['shipment' => $shipmentMock, 'label' => $labelMock]);

        $mergeMock = $this->getFakeMock(Merge::class, true);
        $pdfsExpects = $mergeMock->expects($this->once());
        $pdfsExpects->method('files');
        $pdfsExpects->willReturn('randomstring');

        /** @var Generate $instance */
        $instance = $this->getInstance([
            'prepare' => $prepareMock,
            'merge' => $mergeMock,
        ]);

        $this->assertEquals('randomstring', $instance->run($this->getLabels()));
    }

    /**
     * @return array
     */
    private function getLabels()
    {
        $output = [];
        foreach (range(1, 2) as $index) {
            $mock = $this->getMock(\TIG\PostNL\Api\Data\ShipmentLabelInterface::class);

            $output[] = $mock;
        }

        return $output;
    }
}
