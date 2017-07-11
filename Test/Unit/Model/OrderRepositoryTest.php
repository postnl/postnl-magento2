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

use TIG\PostNL\Service\Filter\SearchResults;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use TIG\PostNL\Model\OrderFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

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

        $listResults = $this->getList(true);

        $createExpects = $builder->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($listResults['searchCreteria']);

        $instance = $this->getInstance([
            'collectionFactory'     => $listResults['collectionFactory'],
            'searchResults'         => $listResults['searchResult'],
            'searchCriteriaBuilder' => $builder
        ]);

        $result = $instance->getByFieldWithValue($field, $value);

        $this->assertEquals($listResults['searchResultFactory'], $result);

    }

    public function testGetList()
    {
        $listData = $this->getList();
        $instance = $this->getInstance([
            'collectionFactory'    => $listData['collectionFactory'],
            'searchResults' => $listData['searchResult']
        ]);

        $result = $instance->getList($listData['searchCreteria']);

        $this->assertEquals($listData['searchResultFactory'], $result);
    }

    /**
     * @param      $field
     * @param      $value
     * @param bool $isFieldWithValue
     *
     * @return array
     */
    private function getList($isFieldWithValue = false)
    {
        $searchCriteria = $this->getMock(SearchCriteria::class, [], [], '', false);

        $orderCollection = $this->getMockForAbstractClass(
            AbstractCollection::class,
            [],
            '',
            false
        );

        $orderCollectionFactory = $this->getFakeMock(CollectionFactory::class)->setMethods(['create'])->getMock();
        $orderCollectionFactory->expects($this->once())->method('create')->willReturn($orderCollection);

        $searchResultFactory = $this->getFakeMock(SearchResultsInterfaceFactory::class)
            ->setMethods(['getTotalCount', 'getItems'])
            ->getMock();

        if ($isFieldWithValue) {
            $searchResultFactory->expects($this->once())->method('getTotalCount')->willReturn(1);
            $searchResultFactory->expects($this->once())->method('getItems')->willReturn([$searchResultFactory]);
        }

        $searchResult = $this->getFakeMock(SearchResults::class)
            ->setMethods(['getCollectionItems'])
            ->getMock();
        $searchResult->expects($this->once())->method('getCollectionItems')->willReturn($searchResultFactory);

        return [
            'searchResultFactory' => $searchResultFactory,
            'searchResult'        => $searchResult,
            'searchCreteria'      => $searchCriteria,
            'collectionFactory'   => $orderCollectionFactory
        ];
    }

    private function getOrderFactoryMock()
    {
        $orderFactoryMock = $this->getFakeMock(OrderFactory::class);
        $orderFactoryMock->setMethods(['create', 'load', 'getId']);
        $orderFactoryMock = $orderFactoryMock->getMock();

        return $orderFactoryMock;
    }
}
