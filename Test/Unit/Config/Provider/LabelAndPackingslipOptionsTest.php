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
namespace TIG\PostNL\Test\Unit\Config\Provider;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order;
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Service\Wrapper\StoreInterface;

class LabelAndPackingslipOptionsTest extends AbstractConfigurationTest
{
    protected $instanceClass = LabelAndPackingslipOptions::class;

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
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
    public function testGetCustomReference($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(LabelAndPackingslipOptions::XPATH_LABEL_PACKINGSLIP_CUSTOM_REFERENCE, $value);
        $this->assertEquals($value, $instance->getCustomReference());
    }

    /**
     * @return array
     */
    public function getCustomReferenceParsedProvider()
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
    public function testGetCustomReferenceParsed($customReference, $orderId, $shipmentId, $frontendName, $expected)
    {
        $storeWrapperMock = $this->getMockBuilder(StoreInterface::class)
            ->setMethods(['getStore', 'getFrontendName'])
            ->getMockForAbstractClass();
        $storeWrapperMock->expects($this->once())->method('getStore')->willReturnSelf();
        $storeWrapperMock->expects($this->once())->method('getFrontendName')->willReturn($frontendName);

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId'])
            ->getMock();
        $orderMock->expects($this->once())->method('getIncrementId')->willReturn($orderId);

        $shipmentMock = $this->getMockBuilder(ShipmentInterface::class)
            ->setMethods(['getOrder', 'getIncrementId'])
            ->getMockForAbstractClass();
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
    public function testGetShowLabel($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(LabelAndPackingslipOptions::XPATH_LABEL_PACKINGSLIP_SHOW_LABEL, $value);
        $this->assertEquals($value, $instance->getShowLabel());
    }
}
