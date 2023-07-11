<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Config\Form\Field;

use TIG\PostNL\Test\TestCase;
use Magento\Framework\Data\Form\Element\AbstractElement;
use TIG\PostNL\Block\Adminhtml\Config\Form\Field\Color;

class ColorTest extends TestCase
{
    protected $instanceClass = Color::class;

    public function testGetElementHtml()
    {
        $elementMock = $this->getFakeMock(AbstractElement::class)->setMethods(['getElementHtml', 'getData', 'getHtmlId'])
            ->getMockForAbstractClass();
        $elementMock->expects($this->once())->method('getElementHtml')->willReturn('<html></html>');
        $elementMock->expects($this->once())->method('getData')->willReturn('#FFFFFF');
        $elementMock->expects($this->once())->method('getHtmlId')->willReturn('background-color');

        $instance = $this->getInstance();
        $result = $this->invokeArgs('_getElementHtml', [$elementMock], $instance);

        $this->assertTrue(false !== strpos($result, '#FFFFFF'));
        $this->assertTrue(false !== strpos($result, 'background-color'));
    }
}
