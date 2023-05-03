<?php

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
     * @param null $searchCriteria
     *
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\PHPUnit\Framework\MockObject\MockObject
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
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getSearchCriteria()
    {
        return $this->getMock(SearchCriteriaInterface::class);
    }
}
