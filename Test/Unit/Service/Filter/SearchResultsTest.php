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
        $criteria = $this->getSearchCriteria();
        $results = $this->getMock(\Magento\Framework\Api\SearchResultsInterface::class);
        $results->expects($this->once())->method('setSearchCriteria')->with($criteria);
        $factory = $this->getMock(SearchResultsInterfaceFactory::class);
        $factory->expects($this->once())->method('create')->willReturn($results);
        $instance = $this->getInstance(['searchResultsFactory' => $factory]);

        $this->assertSame($results, $this->invokeArgs('getSearchResults', [$criteria], $instance));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getSearchCriteria()
    {
        return $this->getMock(SearchCriteriaInterface::class);
    }
}
