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
use Magento\Framework\Api\SearchResultsInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentBarcodeRepositoryInterface;
use TIG\PostNL\Api\Data\ShipmentBarcodeInterface;
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

// @codingStandardsIgnoreFile
/**
 * File became to big, that's why we disabled Code Sniffer checking.
 */
class ShipmentBarcodeRepository implements ShipmentBarcodeRepositoryInterface
{
    /**
     * @var ShipmentBarcodeFactory
     */
    private $shipmentBarcodeFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ShipmentBarcodeFactory        $objectFactory
     * @param CollectionFactory             $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentBarcodeFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentBarcodeFactory = $objectFactory;
        $this->collectionFactory      = $collectionFactory;
        $this->searchResultsFactory   = $searchResultsFactory;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
    }

    /**
     * @param ShipmentBarcodeInterface $shipmentBarcode
     *
     * @return ShipmentBarcodeInterface
     * @throws CouldNotSaveException
     */
    public function save(ShipmentBarcodeInterface $shipmentBarcode)
    {
        try {
            $shipmentBarcode->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $shipmentBarcode;
    }

    /**
     * @param $identifier
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $object = $this->shipmentBarcodeFactory->create();
        $object->load($identifier);

        if (!$object->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $identifier));
        }

        return $object;
    }

    /**
     * @param ShipmentBarcodeInterface $shipmentBarcode
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentBarcodeInterface $shipmentBarcode)
    {
        try {
            $shipmentBarcode->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
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
     * @return SearchResultsInterface
     */
    private function getSearchResults(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        return $searchResults;
    }

    /**
     * Create a PostNL Shipment Barcode.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function create()
    {
        return $this->shipmentBarcodeFactory->create();
    }

    /**
     * @param ShipmentInterface $shipment
     * @param int               $number
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface|null
     */
    public function getForShipment(ShipmentInterface $shipment, $number)
    {
        $shipmentId = $shipment->getId();
        $this->searchCriteriaBuilder->addFilter('parent_id', $shipmentId);
        $this->searchCriteriaBuilder->addFilter('number', $number);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $items = $this->getList($searchCriteria);

        if ($items->getTotalCount()) {
            return $items->getItems()[0];
        }

        return null;
    }
}
