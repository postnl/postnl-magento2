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
