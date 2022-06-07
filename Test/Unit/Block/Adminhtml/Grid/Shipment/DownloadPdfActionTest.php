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

use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use TIG\PostNL\Block\Adminhtml\Grid\Shipment\DownloadPdfAction;
use TIG\PostNL\Test\TestCase;

class DownloadPdfActionTest extends TestCase
{
    public $instanceClass = DownloadPdfAction::class;

    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * {@inheritdoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->contextMock = $contextMock = $this->getFakeMock(Context::class, true);
    }

    /**
     * Make sure the instance always has the context mocks
     *
     * {@inheritdoc}
     */
    public function getInstance(array $args = [])
    {
        if (!isset($args['context'])) {
            $args['context'] = $this->contextMock;
        }

        return parent::getInstance($args);
    }

    private function prepareContextMock()
    {
        $urlBuilderMock = $this->getFakeMock(UrlInterface::class)->getMockForAbstractClass();
        $this->contextMock->expects($this->atLeastOnce())->method('getUrlBuilder')->willReturn($urlBuilderMock);
    }

    /**
     * All getUrl() download methed are exactly the same, minus the url path and returned url.
     * This method is set up in a generic way that you only need to give these differences and the method.
     *
     * @param $path
     * @param $returnUrl
     * @param $method
     */
    private function assertDownloadUrl($path, $returnUrl, $method)
    {
        $this->prepareContextMock();

        /** @var UrlInterface|\PHPUnit\Framework\MockObject\MockObject $urlBuilderMock */
        $urlBuilderMock = $this->contextMock->getUrlBuilder();
        $urlBuilderMock->expects($this->once())->method('getUrl')->with($path, [])->willReturn($returnUrl);

        $instance = $this->getInstance();
        $result = $instance->$method();

        $this->assertIsString($result);
        $this->assertEquals($returnUrl, $result);
    }

    public function testGetDownloadUrl()
    {
        $this->assertDownloadUrl('postnl/shipment/massPrintShippingLabel', 'tig.nl', 'getDownloadUrl');
    }

    public function testGetPrintPackingSlipUrl()
    {
        $this->assertDownloadUrl('postnl/shipment/massPrintPackingslip', 'tig.nl', 'getPrintPackingSlipUrl');
    }
}
