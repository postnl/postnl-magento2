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
namespace TIG\PostNL\Unit\Service\Filter;

use TIG\PostNL\Service\Filter\SearchResults;
use TIG\PostNL\Test\TestCase;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class SearchResultsTest extends TestCase
{
    protected $instanceClass = SearchResults::class;

    public function testGetCollectionItems()
    {
        $listData = $this->getList('order_id', 10);

        $instance = $this->getInstance([
            'searchResultsFactory' => $listData['searchResult']
        ]);

        $result = $instance->getCollectionItems($listData['searchCreteria'], $listData['collectionFactory']);

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
        $filter->expects($this->atLeastOnce())->method('getValue')->willReturn($value);

        $orderCollection = $this->getMock(
            AbstractCollection::class,
            ['create', 'addFieldToFilter', 'getSize', 'setCurPage', 'setPageSize', 'load'],
            [],
            '',
            false
        );

        $orderCollection->expects($this->atLeastOnce())->method('load')->willReturnSelf();
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

    /**
     * @param null $searchCriteria
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSearchCriteria()
    {
        return $this->getMock(SearchCriteriaInterface::class);
    }
}
