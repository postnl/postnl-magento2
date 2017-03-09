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

use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ResourceModel\Shipment\CollectionFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

// @codingStandardsIgnoreFile
/**
 * This file is too big to apply with the coding standards.
 */
class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ShipmentFactory               $shipmentFactory
     * @param CollectionFactory             $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentFactory       = $shipmentFactory;
        $this->collectionFactory     = $collectionFactory;
        $this->searchResultsFactory  = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShipmentInterface
     * @throws CouldNotSaveException
     */
    public function save(ShipmentInterface $shipment)
    {
        try {
            $shipment->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $shipment;
    }

    /**
     * @param $identifier
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $shipment = $this->shipmentFactory->create();
        $shipment->load($identifier);

        if (!$shipment->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Shipment with id "%1" does not exist.', $identifier));
        }

        return $shipment;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentInterface $shipment)
    {
        try {
            $shipment->delete();
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
        $shipments = [];
        foreach ($collection as $shipmentModel) {
            $shipments[] = $shipmentModel;
        }

        $searchResults->setItems($shipments);

        return $searchResults;
    }

    /**
     * @param $filterGroup
     * @param $collection
     */
    private function handleFilterGroups($filterGroup, $collection)
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
    private function handleSortOrders(SearchCriteriaInterface $criteria, $collection)
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
    private function getSearchResults(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        return $searchResults;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface|null
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
     * Retrieve a specific PostNL shipment by the Magento Shipment ID.
     *
     * @param int $identifier
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface|null
     */
    public function getByShipmentId($identifier)
    {
        return $this->getByFieldWithValue('shipment_id', $identifier);
    }

    /**
     * Delete a PostNL shipment.
     *
     * @api
     *
     * @param int $identifier
     *
     * @return bool
     */
    public function deleteById($identifier)
    {
        $shipment = $this->getById($identifier);

        return $this->delete($shipment);
    }

    /**
     * Create a PostNL shipment.
     *
     * @api
     * @param array $data
     * @return Shipmentinterface
     */
    public function create(array $data = [])
    {
        return $this->shipmentFactory->create($data);
    }
}
