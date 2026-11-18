<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use Magento\Sales\Model\Order\Shipment as ShipmentInterface;
use Magento\Sales\Model\Order;
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Service\Wrapper\StoreInterface;

class LabelAndPackingslipOptionsTest extends AbstractConfiguration
{
    protected $instanceClass = LabelAndPackingslipOptions::class;

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\TIG\PostNL\Test\Fixtures\DataProvider::class, 'randomWordsProvider')]
    public function testGetReferenceType($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(LabelAndPackingslipOptions::XPATH_LABEL_PACKINGSLIP_REFERENCE_TYPE, $value);
        $this->assertEquals($value, $instance->getReferenceType());
    }

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\TIG\PostNL\Test\Fixtures\DataProvider::class, 'randomWordsProvider')]
    public function testGetCustomReference($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(LabelAndPackingslipOptions::XPATH_LABEL_PACKINGSLIP_CUSTOM_REFERENCE, $value);
        $this->assertEquals($value, $instance->getCustomReference());
    }

    /**
     * @return array
     */
    public static function getCustomReferenceParsedProvider()
    {
        return [
            'replace order ID' => [
                'order id: {{var order_increment_id}}',
                1,
                2,
                'TIG Store',
                'order id: 1'
            ],
            'replace shipment ID' => [
                'shipment id: {{var shipment_increment_id}}',
                3,
                4,
                'TIG Store',
                'shipment id: 4'
            ],
            'replace frontend name' => [
                'frontend name: {{var store_frontend_name}}',
                5,
                6,
                'TIG Store',
                'frontend name: TIG Store'
            ],
            'replace all' => [
                'order id: {{var order_increment_id}} shipment id: {{var shipment_increment_id}} frontend name: {{var store_frontend_name}}',
                9,
                10,
                'TIG Store',
                'order id: 9 shipment id: 10 frontend name: TIG Store'
            ],
        ];
    }

    /**
     * @param $customReference
     * @param $orderId
     * @param $shipmentId
     * @param $frontendName
     * @param $expected
     *
     * @dataProvider getCustomReferenceParsedProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getCustomReferenceParsedProvider')]
    public function testGetCustomReferenceParsed($customReference, $orderId, $shipmentId, $frontendName, $expected)
    {
        $storeWrapperMock = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store = $this->createStub(\Magento\Store\Model\Store::class);
        $store->method('getFrontendName')->willReturn($frontendName);
        $storeWrapperMock->expects($this->once())->method('getStore')->willReturn($store);

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIncrementId'])
            ->getMock();
        $orderMock->expects($this->once())->method('getIncrementId')->willReturn($orderId);

        $shipmentMock = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOrder', 'getIncrementId'])
            ->getMock();
        $shipmentMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $shipmentMock->expects($this->once())->method('getIncrementId')->willReturn($shipmentId);

        $instance = $this->getInstance(['storeWrapper' => $storeWrapperMock]);
        $this->setXpath(LabelAndPackingslipOptions::XPATH_LABEL_PACKINGSLIP_CUSTOM_REFERENCE, $customReference);

        $result = $instance->getCustomReferenceParsed($shipmentMock);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\TIG\PostNL\Test\Fixtures\DataProvider::class, 'randomWordsProvider')]
    public function testGetShowLabel($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(LabelAndPackingslipOptions::XPATH_LABEL_PACKINGSLIP_SHOW_LABEL, $value);
        $this->assertEquals($value, $instance->getShowLabel());
    }
}
