<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Shipment\Options;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Test\TestCase;

class ViewTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Block\Adminhtml\Shipment\Options\View::class;

    public function canChangeParcelCountProvider()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider canChangeParcelCountProvider
     */
    public function testCanChangeParcelCount($canChange)
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->getMock(ShipmentInterface::class);

        $canChangeParcelCount = $shipment->method('canChangeParcelCount');
        $canChangeParcelCount->willReturn($canChange);

        $instance = $this->getInstance();
        $this->setProperty('shipment', $shipment, $instance);
        $this->assertEquals($canChange, $instance->canChangeParcelCount());
    }
}
