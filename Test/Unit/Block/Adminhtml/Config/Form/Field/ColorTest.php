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
