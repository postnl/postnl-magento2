<?php

namespace TIG\PostNL\Test\Unit\Helper;

use Magento\Sales\Model\Order;
use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\Webshop;

class DataTest extends TestCase
{
    protected $instanceClass = Helper::class;

    public function isPostNLOrderProvider()
    {
        return [
            ['tig_postnl_regular', true],
            ['dhl_regular', false],
        ];
    }

    /**
     * @param $shippingMethod
     * @param $expected
     *
     * @dataProvider isPostNLOrderProvider
     */
    public function testIsPostNLOrder($shippingMethod, $expected)
    {
        /** @var Order $order */
        $order = $this->objectManager->getObject(Order::class);
        $order->setData('shipping_method', $shippingMethod);

        $webshopMock = $this->getFakeMock(Webshop::class)->getMock();
        $webshopExpects = $webshopMock->expects($this->once());
        $webshopExpects->method('getAllowedShippingMethods')->with(0);
        $webshopExpects->willReturn(['tig_postnl_regular']);

        $instance = $this->getInstance([
            'webshop' => $webshopMock
        ]);

        /** @var bool $result */
        $result = $instance->isPostNLOrder($order);

        $this->assertEquals($expected, $result);
    }

    public function testGetAllowedDeliveryOptions()
    {
        $result = $this->getInstance()->getAllowedDeliveryOptions();
        $this->assertTrue(is_array($result));
    }

    public function testGetAllowedDeliveryOptionsHasPakjeGemak()
    {
        $shippingOptionsConfigurationMock = $this->getFakeMock(ShippingOptions::class)->getMock();
        $isPakjeGemakActiveExpects = $shippingOptionsConfigurationMock->expects($this->once());
        $isPakjeGemakActiveExpects->method('isPakjegemakActive');
        $isPakjeGemakActiveExpects->willReturn(true);

        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptionsConfigurationMock
        ]);

        $result = $instance->getAllowedDeliveryOptions();
        $this->assertContains('PG', $result);
    }

    public function formatXmlProvider()
    {
        return [
            ['<root><node>value</node></root>', '<?xml version="1.0"?>
<root>
  <node>value</node>
</root>
'],
        ];
    }

    /**
     * @param $xml
     * @param $expected
     *
     * @dataProvider formatXmlProvider
     */
    public function testFormatXml($xml, $expected)
    {
        $result = $this->invokeArgs('formatXml', [$xml]);

        $this->assertEquals($expected, $result);
    }
}
