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

use Magento\Sales\Model\Order as SalesOrder;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \TIG\PostNL\Model\Order as PostNLOrder;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param OrderFactory                  $orderFactory
     * @param CollectionFactory             $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     */
    public function __construct(
        OrderFactory $orderFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderFactory          = $orderFactory;
        $this->collectionFactory     = $collectionFactory;
        $this->searchResultsFactory  = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order)
    {
        try {
            $order->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $order;
    }

    /**
     * @param $identifier
     *
     * @return \Magento\Framework\DataObject
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $object = $this->orderFactory->create();
        $object->load($identifier);

        if (!$object->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $identifier));
        }

        return $object;
    }

    /**
     * @param array $data
     *
     * @return Order
     */
    public function create(array $data = [])
    {
        return $this->orderFactory->create($data);
    }

    /**
     * @param $field
     * @param $value
     *
     * @return PostNLOrder|null
     */
    public function getByFieldWithValue($field, $value)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter($field, $value);
        $searchCriteria->setPageSize(1);

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->getList($searchCriteria->create());

        if ($list->getTotalCount()) {
            return $list->getItems()[0];
        }

        return null;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(OrderInterface $order)
    {
        try {
            $order->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
     * @return mixed
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
        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }

        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @param $filterGroup
     * @param $collection
     */
    public function handleFilterGroups($filterGroup, $collection)
    {
        $fields     = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
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
    public function handleSortOrders(SearchCriteriaInterface $criteria, $collection)
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
     * @param SearchCriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getSearchResults(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        return $searchResults;
    }
}
