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
namespace TIG\PostNL\Unit\Model;

use TIG\PostNL\Test\TestCase;
use \TIG\PostNL\Model\OrderRepository;
use \TIG\PostNL\Model\Order;
use \TIG\PostNL\Api\Data\OrderInterface;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use \TIG\PostNL\Model\OrderFactory;
use \Magento\Framework\Api\SearchResultsInterfaceFactory;
use \Magento\Framework\Api\Search\FilterGroup;
use \Magento\Framework\Api\Filter;
use \Magento\Framework\Api\SearchCriteria;

class OrderRepositoryTest extends TestCase
{
    protected $instanceClass = OrderRepository::class;

    public function testSave()
    {
        $orderInterface = $this->getFakeMock(Order::class);
        $orderInterface->setMethods(['getIdentities', 'save']);
        $orderInterface = $orderInterface->getMock();

        $saveExpects = $orderInterface->expects($this->once());
        $saveExpects->method('save');

        $instance = $this->getInstance();
        $result   = $instance->save($orderInterface);

        $this->assertEquals($orderInterface, $result);
    }

    public function testCreate()
    {
        $orderFactoryMock = $this->getOrderFactoryMock();
        $data             = [
            'order_id' => 10,
            'quote_id' => 12
        ];

        $createExpects = $orderFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->with($data);
        $createExpects->willReturnSelf();

        $instance = $this->getInstance(['orderFactory' => $orderFactoryMock]);
        $result   = $instance->create($data);

        $this->assertEquals($orderFactoryMock, $result);
    }

    public function testGetById()
    {
        $orderId = rand(10, 100);
        $orderFactoryMock = $this->getOrderFactoryMock();

        $createExpects = $orderFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $loadExpects = $orderFactoryMock->expects($this->once());
        $loadExpects->method('load');
        $loadExpects->willReturnSelf();

        $getIdExpects = $orderFactoryMock->expects($this->once());
        $getIdExpects->method('getId');
        $getIdExpects->willReturn($orderId);

        $instance = $this->getInstance(['orderFactory' => $orderFactoryMock]);
        $result   = $instance->getById($orderId);

        $this->assertEquals($orderFactoryMock, $result);
    }

    /**
     * @param string $field
     * @param int    $value
     */
    public function testGetFieldWithValue($field = 'order_id', $value = 10)
    {

        $builder = $this->getFakeMock(SearchCriteriaBuilder::class)->getMock();

        $filterExpects = $builder->expects($this->once());
        $filterExpects->method('addFilter');
        $filterExpects->with($field, $value);
        $filterExpects->willReturnSelf();

        $pageSizeExpects = $builder->expects($this->once());
        $pageSizeExpects->method('setPageSize');
        $pageSizeExpects->with(1);
        $pageSizeExpects->willReturnSelf();

        $listResults = $this->getList($field, $value, true);

        $createExpects = $builder->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($listResults['searchCreteria']);

        $instance = $this->getInstance([
            'collectionFactory'     => $listResults['collectionFactory'],
            'searchResultsFactory'  => $listResults['searchResult'],
            'searchCriteriaBuilder' => $builder
        ]);

        $result = $instance->getByFieldWithValue($field, $value);

        $this->assertEquals($listResults['searchResult'], $result);

    }

    public function testGetList()
    {
        $listData = $this->getList('order_id', 10);
        $instance = $this->getInstance([
            'collectionFactory'    => $listData['collectionFactory'],
            'searchResultsFactory' => $listData['searchResult']
        ]);

        $result = $instance->getList($listData['searchCreteria']);

        $this->assertEquals($listData['searchResult'], $result);
    }

    public function testGetSearchResults()
    {
        $searchResults = $this->getSearchResults();

        $instance = $this->getInstance([
            'searchResultsFactory'  => $searchResults
        ]);

        $result = $this->invokeArgs('getSearchResults', [$this->getSearchCriteria()], $instance);

        $this->assertEquals($searchResults, $result);
    }

    /**
     * @param      $field
     * @param      $value
     * @param bool $isFieldWithValue
     *
     * @return array
     */
    private function getList($field, $value, $isFieldWithValue = false)
    {
        $filterGroup    = $this->getMock(FilterGroup::class, [], [], '', false);
        $filter         = $this->getMock(Filter::class, [], [], '', false);
        $searchCriteria = $this->getMock(SearchCriteria::class, [], [], '', false);

        $searchCriteria->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroup]);
        $filterGroup->expects($this->once())->method('getFilters')->willReturn([$filter]);
        $filter->expects($this->exactly(2))->method('getConditionType')->willReturn('eq');
        $filter->expects($this->atLeastOnce())->method('getField')->willReturn($field);
        $filter->expects($this->once())->method('getValue')->willReturn($value);

        $orderCollection = $this->getMock(
            CollectionFactory::class,
            ['create', 'addFieldToFilter', 'getSize', 'setCurPage', 'setPageSize'],
            [],
            '',
            false
        );

        $orderCollection->expects($this->atLeastOnce())->method('create')->willReturnSelf();
        $orderCollection->expects($this->once())->method('setCurPage')->with(1);
        $searchCriteria->expects($this->once())->method('getCurrentPage')->willReturn(1);
        $orderCollection->expects($this->once())->method('setPageSize')->with(1);
        $searchCriteria->expects($this->once())->method('getPageSize')->willReturn(1);
        $orderCollection->expects($this->once())->method('addFieldToFilter')->withAnyParameters();
        $orderCollection->expects($this->once())->method('getSize')->willReturn(1);

        $objects = [];

        foreach ($orderCollection as $objectModel) {
            $objects[] = $objectModel;
        }

        $searchResultFactory = $this->getSearchResults($searchCriteria);
        $searchResultFactory->expects($this->once())->method('setTotalCount')->with(1);

        if ($isFieldWithValue) {
            $searchResultFactory->expects($this->once())->method('getTotalCount')->willReturn(1);
            $searchResultFactory->expects($this->once())->method('getItems')->willReturn([$searchResultFactory]);
        }

        $searchResultFactory->expects($this->once())->method('setItems')->with($objects)->willReturnSelf();

        return [
            'searchResult'      => $searchResultFactory,
            'searchCreteria'    => $searchCriteria,
            'collectionFactory' => $orderCollection
        ];
    }

    private function getSearchResults($searchCriteria = null)
    {
        $searchResults = $this->getFakeMock(SearchResultsInterfaceFactory::class);
        $searchResults->setMethods(['create','setSearchCriteria', 'setTotalCount', 'setItems', 'getTotalCount', 'getItems']);
        $searchResults = $searchResults->getMock();

        $expectsCreate = $searchResults->expects($this->once());
        $expectsCreate->method('create');
        $expectsCreate->willReturnSelf();

        $searchCriteria = $searchCriteria == null ? $this->getSearchCriteria() : $searchCriteria;

        $expectsSetSearchCriteria = $searchResults->expects($this->once());
        $expectsSetSearchCriteria->method('setSearchCriteria');
        $expectsSetSearchCriteria->with($searchCriteria);
        $expectsSetSearchCriteria->willReturnSelf();

        return $searchResults;
    }

    private function getOrderFactoryMock()
    {
        $orderFactoryMock = $this->getFakeMock(OrderFactory::class);
        $orderFactoryMock->setMethods(['create', 'load', 'getId']);
        $orderFactoryMock = $orderFactoryMock->getMock();

        return $orderFactoryMock;
    }

    private function getSearchCriteria()
    {
        return $this->getMock(SearchCriteriaInterface::class);
    }
}
