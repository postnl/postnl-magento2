<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Grid\Order;

use Magento\Framework\UrlInterface;
use TIG\PostNL\Block\Adminhtml\Grid\Order\DownloadPdfAction;
use TIG\PostNL\Test\TestCase;

class DownloadPdfActionTest extends TestCase
{
    public $instanceClass = DownloadPdfAction::class;


    public function testGetConfirmAndPrintLabelsUrl()
    {
        $urlResultMock = "tig.nl";

        $urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMockForAbstractClass();
        $urlBuilderMock->method('getUrl')->willReturn($urlResultMock);

        $instance = $this->getInstance();
        $this->setProperty('_urlBuilder', $urlBuilderMock, $instance);
        $result = $instance->getConfirmAndPrintLabelsUrl();

        $this->assertIsString($result);
        $this->assertEquals($urlResultMock, $result);
    }
}
