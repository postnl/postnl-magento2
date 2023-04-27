<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Grid\Shipment;

use TIG\PostNL\Block\Adminhtml\Grid\Shipment\ShippingDate;
use TIG\PostNL\Test\TestCase;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ShippingDateTest extends TestCase
{
    protected $instanceClass = ShippingDate::class;

    /**
     * @param array $args
     *
     * @return ShippingDate
     */
    public function getInstance(array $args = [])
    {
        if (!isset($args['context'])) {
            $contextMock = $this->getMockForAbstractClass(ContextInterface::class, [], '', false, true, true, []);
            $processor   = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
                ->disableOriginalConstructor()
                ->getMock();
            $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);

            $args['context'] = $contextMock;
        }

        return parent::getInstance($args);
    }

    public function testGetCellContents()
    {
        $randomString = uniqid();
        $randomResult = uniqid();

        $rendererMock = $this->getFakeMock(\TIG\PostNL\Block\Adminhtml\Renderer\ShippingDate::class)->getMock();

        $renderExpects = $rendererMock->expects($this->once());
        $renderExpects->method('render');
        $renderExpects->with($randomString);
        $renderExpects->willReturn($randomResult);

        $instance = $this->getInstance([
            'shippingDateRenderer' => $rendererMock,
        ]);

        $result = $this->invokeArgs('getCellContents', [['tig_postnl_ship_at' => $randomString]], $instance);

        $this->assertEquals($randomResult, $result);
    }
}
