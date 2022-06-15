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
