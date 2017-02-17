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
use \TIG\PostNL\Model\OrderInterface;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use \TIG\PostNL\Model\OrderFactory;
use \Magento\Framework\Api\SearchResults;
use \Magento\Framework\Api\SearchResultsInterfaceFactory;
use \Magento\Framework\Api\Search\FilterGroup;

class OrderRepositoryTest extends TestCase
{
    protected $instanceClass = OrderRepository::class;

    public function testSave()
    {
        $orderInterface = $this->getMock(OrderInterface::class, ['getIdentities', 'save']);

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
        $this->markTestSkipped('Rik : Needs to be finished, getList method is a pain in the ...');
        $order = $this->getOrderReturned();

        $filter = $this->getObject('Magento\Framework\Api\Filter');
        $filter->setField($field);
        $filter->setValue($value);

        $filterGroup = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroup->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);

        $criteria = $this->getSearchCriteria();
        $expectsFilterGroups = $criteria->expects($this->once());
        $expectsFilterGroups->method('getFilterGroups');
        $expectsFilterGroups->willReturn([$filterGroup]);

        $orderRepositoryMock = $this->getFakeMock(OrderRepository::class)->getMock();
        $geListExpects = $orderRepositoryMock->expects($this->once());
        $geListExpects->method('getList');
        $geListExpects->with($criteria);
        $geListExpects->willReturn($order);

        $collectionMock = $this->getFakeMock(CollectionFactory::class);
        $collectionMock->setMethods(['create']);
        $collectionMock = $collectionMock->getMock();

        $expectsCreate = $collectionMock->expects($this->once());
        $expectsCreate->method('create');
        $expectsCreate->willReturnSelf();

        $instance = $this->getInstance([
            'collectionFactory'     => $collectionMock,
            'searchResultsFactory'  => $this->getSearchResults(),
            'searchCriteriaBuilder' => $this->getSearchCriteriaBuilder($field, $value)
        ]);
        $result = $instance->getByFieldWithValue($field, $value);

        $this->assertEquals($order, $result);
    }

    public function testGetSearchResults()
    {
        $searchResults = $this->getSearchResults();

        $instance = $this->getInstance([
            'searchResultsFactory'  => $searchResults
        ]);

        $result = $instance->getSearchResults($this->getSearchCriteria());

        $this->assertEquals($searchResults, $result);
    }


    private function getSearchResults()
    {
        $searchResults = $this->getFakeMock(SearchResultsInterfaceFactory::class);
        $searchResults->setMethods(['create','setSearchCriteria']);
        $searchResults = $searchResults->getMock();

        $expectsCreate = $searchResults->expects($this->once());
        $expectsCreate->method('create');
        $expectsCreate->willReturnSelf();

        $expectsSetSearchCriteria = $searchResults->expects($this->once());
        $expectsSetSearchCriteria->method('setSearchCriteria');
        $expectsSetSearchCriteria->with($this->getSearchCriteria());
        $expectsSetSearchCriteria->willReturnSelf();

        return $searchResults;
    }

    private function getOrderReturned()
    {
        $searchResults = $this->getMock(SearchResults::class);

        $getTotalCountExpects = $searchResults->expects($this->once());
        $getTotalCountExpects->method('getTotalCount');
        $getTotalCountExpects->willReturn(1);

        $getItemsExpects = $searchResults->expects($this->once());
        $getItemsExpects->method('getItems');
        $getItemsExpects->willReturn(1);

        return $searchResults;
    }

    private function getSearchCriteriaBuilder($field, $value)
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

        $createExpects = $builder->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($this->getSearchCriteria());

        return $builder;
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
