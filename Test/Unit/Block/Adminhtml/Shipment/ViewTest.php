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
namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Shipment;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Block\Adminhtml\Shipment\View;
use TIG\PostNL\Test\TestCase;

class ViewTest extends TestCase
{
    public $instanceClass = View::class;

    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * @var Registry|\PHPUnit\Framework\MockObject\MockObject
     */
    private $registryMock;

    /**
     * {@inheritdoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->contextMock = $this->getFakeMock(Context::class, true);
        $this->registryMock = $this->getFakeMock(Registry::class, true);
    }

    /**
     * Make sure the instance always has the context and registry mocks
     *
     * {@inheritdoc}
     */
    public function getInstance(array $args = [])
    {
        if (!isset($args['context'])) {
            $args['context'] = $this->contextMock;
        }

        if (!isset($args['registry'])) {
            $args['registry'] = $this->registryMock;
        }

        return parent::getInstance($args);
    }

    /**
     * Fill the context object with minimal data necessary for the instance
     */
    private function prepareContextMock()
    {
        $urlBuilderMock = $this->getFakeMock(UrlInterface::class, true);
        $requestMock = $this->getFakeMock(RequestInterface::class, true);
        $authorizationMock = $this->getFakeMock(AuthorizationInterface::class)->getMockForAbstractClass();

        $buttonListMock = $this->getFakeMock(ButtonList::class)->setMethods(['add'])->getMock();
        $buttonListMock->expects($this->atLeastOnce())->method('add');

        $this->contextMock->method('getUrlBuilder')->willReturn($urlBuilderMock);
        $this->contextMock->method('getButtonList')->willReturn($buttonListMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($requestMock);
        $this->contextMock->expects($this->once())->method('getAuthorization')->willReturn($authorizationMock);
    }

    /**
     * Fill the registry object with minimal data necessary for the instance
     */
    private function prepareRegistryMock()
    {
        $orderMock = $this->getFakeMock(Order::class, true);
        $shipmentMock = $this->getFakeMock(Shipment::class)->setMethods(['getOrder', 'getId'])->getMock();
        $shipmentMock->method('getOrder')->willReturn($orderMock);
        $shipmentMock->method('getId')->willReturn(1);

        $this->registryMock->method('registry')->with('current_shipment')->willReturn($shipmentMock);
    }

    public function testSetPostNLPrintPackingslipButton()
    {
        $this->prepareContextMock();
        $this->prepareRegistryMock();
        $instance = $this->getInstance();

        /** @var ButtonList|\PHPUnit\Framework\MockObject\MockObject $buttonListMock */
        $buttonListMock = $this->contextMock->getButtonList();
        $buttonListMock->expects($this->exactly(1))->method('add')->with(
            'postnl_print_packingslip',
            ['label' => __('PostNL - Print Packingslip'), 'class' => 'save primary', 'onclick' => 'download(\'\')']
        );

        $this->invoke('setPostNLPrintPackingslipButton', $instance);
    }

    public function testGetPackingslipUrl()
    {
        $this->prepareContextMock();
        $this->prepareRegistryMock();
        $insance = $this->getInstance();

        /** @var UrlInterface|\PHPUnit\Framework\MockObject\MockObject $urlBuilderMock */
        $urlBuilderMock = $this->contextMock->getUrlBuilder();
        $urlBuilderMock->expects($this->exactly(1))
            ->method('getUrl')
            ->with('postnl/shipment/PrintPackingslip', ['shipment_id' => 1])
            ->willReturn('https://printpackingslip.com');

        $result = $this->invoke('getPackingslipUrl', $insance);

        $this->assertEquals('https://printpackingslip.com', $result);
    }
}
