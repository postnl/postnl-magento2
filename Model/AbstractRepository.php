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

namespace TIG\PostNL\Model;

use Magento\Cms\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;

abstract class AbstractRepository
{
    /**
     * @var CollectionFactory
     */
    // @codingStandardsIgnoreLine
    protected $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    // @codingStandardsIgnoreLine
    protected $searchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    // @codingStandardsIgnoreLine
    protected $searchCriteriaBuilder;

    /**
     * @var SearchResultsInterface
     */
    // @codingStandardsIgnoreLine
    protected $searchResults;

    /**
     * AbstractRepository constructor.
     *
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchResultsFactory  = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve a list of Matrixrates.
     *
     * @param SearchCriteriaInterface $criteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->getSearchResults($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $this->handleFilterGroups($filterGroup, $collection);
        }

        $searchResults->setTotalCount($collection->getSize());
        $this->handleSortOrders($criteria, $collection);

        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
     * @return SearchResultsInterface
     */
    // @codingStandardsIgnoreLine
    protected function getSearchResults(SearchCriteriaInterface $criteria)
    {
        $this->searchResults = $this->searchResultsFactory->create();
        $this->searchResults->setSearchCriteria($criteria);

        return $this->searchResults;
    }

    /**
     * @param FilterGroup        $filterGroup
     * @param AbstractCollection $collection
     */
    // @codingStandardsIgnoreLine
    protected function handleFilterGroups($filterGroup, $collection)
    {
        $fields     = [];
        $conditions = [];

        /** @var Filter[] $filters */
        $filters = array_filter($filterGroup->getFilters(), function (Filter $filter) {
            return !empty($filter->getValue());
        });

        foreach ($filters as $filter) {
            $condition    = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[]     = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @param                         $collection
     */
    // @codingStandardsIgnoreLine
    protected function handleSortOrders(SearchCriteriaInterface $criteria, $collection)
    {
        $sortOrders = $criteria->getSortOrders();

        if (!$sortOrders) {
            return;
        }

        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $collection->addOrder(
                $sortOrder->getField(),
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
    }

    /**
     * @param $field
     * @param $value
     *
     * @return AbstractModel|null
     */
    public function getByFieldWithValue($field, $value)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter($field, $value);
        $searchCriteria->setPageSize(1);

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->getList($searchCriteria->create());

        if ($list->getTotalCount()) {
            $items = $list->getItems();
            return array_shift($items);
        }

        return null;
    }
}
