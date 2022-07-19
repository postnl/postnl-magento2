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
