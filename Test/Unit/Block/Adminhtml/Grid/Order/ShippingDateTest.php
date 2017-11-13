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

use TIG\PostNL\Block\Adminhtml\Grid\Order\ShippingDate;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use TIG\PostNL\Test\TestCase;

class ShippingDateTest extends TestCase
{
    public $instanceClass = ShippingDate::class;

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

    public function testPrepareData()
    {
        $items = [
            $this->getModelMock(2),
            $this->getModelMock(4),
        ];

        $searchCriteria = $this->getSearchCriteria();
        $criteriaBuilder = $this->getCriteriaBuilder($searchCriteria, [2, 4, 6]);
        $shipmentRepository = $this->getShipmentRepository($searchCriteria, $items);
        $orderRepository = $this->getOrderRepository($searchCriteria);

        $instance = $this->getInstance([
            'searchCriteriaBuilder' => $criteriaBuilder,
            'shipmentRepository' => $shipmentRepository,
            'orderRepository' => $orderRepository,
        ]);

        $items = [
            ['entity_id' => 2],
            ['entity_id' => 4],
            ['entity_id' => 6],
        ];

        $this->setProperty('items', $items, $instance);

        $this->invoke('prepareData', $instance);
    }

    public function getCellContentsProvider()
    {
        return [
            [1, 'model 1', 'model 1<br>'],
            [2, 'model 1', 'model 1<br>model 1<br>'],
            [3, null, null],
        ];
    }

    /**
     * @param $itemId
     * @param $input
     * @param $expected
     *
     * @dataProvider getCellContentsProvider
     */
    public function testGetCellContents($itemId, $input, $expected)
    {
        $shippingDateRenderer = $this
            ->getFakeMock(\TIG\PostNL\Block\Adminhtml\Renderer\ShippingDate::class)
            ->setMethods(['render'])
            ->getMock();

        $renderMethod = $shippingDateRenderer->method('render');
        $renderMethod->willReturn($input);

        $instance = $this->getInstance([
            'shippingDateRenderer' => $shippingDateRenderer,
        ]);

        $this->setProperty('shipments', [
            1 => ['model 1'],
            2 => ['model 1', 'model 2'],
        ], $instance);

        $result = $this->invokeArgs('getCellContents', [['entity_id' => $itemId]], $instance);

        $this->assertEquals($expected, $result);
    }

    public function testLoadShipments()
    {
        $searchCriteria = $this->getSearchCriteria();
        $shipmentRepository = $this->getShipmentRepository($searchCriteria);

        $instance = $this->getInstance([
            'shipmentRepository' => $shipmentRepository,
        ]);
        $this->invokeArgs('loadShipments', [$searchCriteria], $instance);
    }

    public function testLoadOrder()
    {
        $searchCriteria = $this->getSearchCriteria();
        $orderRepository = $this->getOrderRepository($searchCriteria);

        $instance = $this->getInstance([
            'orderRepository' => $orderRepository,
        ]);
        $this->invokeArgs('loadOrders', [$searchCriteria], $instance);
    }


    private function getModelMock($orderId)
    {
        $shipment = $this->getFakeMock(\TIG\PostNL\Model\Shipment::class)->setMethods(['getOrderId'])->getMock();

        $getOrderIdExpects = $shipment->expects($this->once());
        $getOrderIdExpects->method('getOrderId');
        $getOrderIdExpects->willReturn($orderId);

        return $shipment;
    }

    private function getCriteriaBuilder($searchCriteria, $orderIds = [1, 2, 3])
    {
        $criteriaBuilder = $this->getFakeMock(\Magento\Framework\Api\SearchCriteriaBuilder::class)->getMock();

        $addFilterExpects = $criteriaBuilder->expects($this->once());
        $addFilterExpects->method('addFilter');
        $addFilterExpects->with('order_id', $orderIds, 'IN');
        $addFilterExpects->willReturnSelf();

        $createExpects = $criteriaBuilder->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($searchCriteria);

        return $criteriaBuilder;
    }

    private function getShipmentRepository($searchCriteria, $getItemsReturn = [1, 2, 3])
    {
        $shipmentRepository = $this->getFakeMock(\TIG\PostNL\Api\ShipmentRepositoryInterface::class)->getMock();

        $getListExpects = $shipmentRepository->expects($this->once());
        $getListExpects->method('getList');
        $getListExpects->with($searchCriteria);
        $getListExpects->willReturn($this->getSearchResults($getItemsReturn));

        return $shipmentRepository;
    }

    private function getOrderRepository($searchCriteria, $getItemsReturn = [])
    {
        $orderRepository = $this->getFakeMock(\TIG\PostNL\Api\OrderRepositoryInterface::class)->getMock();

        $getListExpects = $orderRepository->expects($this->once());
        $getListExpects->method('getList');
        $getListExpects->with($searchCriteria);
        $getListExpects->willReturn($this->getSearchResults($getItemsReturn));

        return $orderRepository;
    }

    private function getSearchCriteria()
    {
        $searchCriteria = $this->getMock(\Magento\Framework\Api\SearchCriteriaInterface::class);

        return $searchCriteria;
    }

    private function getSearchResults($getItemsReturn = [1, 2, 3])
    {
        $searchResults = $this->getMock(\Magento\Framework\Api\SearchResults::class);

        $getItemsExpects = $searchResults->expects($this->once());
        $getItemsExpects->method('getItems');
        $getItemsExpects->willReturn($getItemsReturn);

        return $searchResults;
    }
}
