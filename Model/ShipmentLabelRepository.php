<?php

namespace TIG\PostNL\Model;

use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory;
use TIG\PostNL\Service\Filter\SearchResults;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ShipmentLabelRepository implements ShipmentLabelRepositoryInterface
{
    /**
     * @var ShipmentLabelFactory
     */
    private $shipmentLabelFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SearchResults
     */
    private $searchResults;

    /**
     * @param ShipmentLabelFactory  $objectFactory
     * @param CollectionFactory     $collectionFactory
     * @param SearchResults         $searchResults
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentLabelFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResults $searchResults,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentLabelFactory  = $objectFactory;
        $this->collectionFactory     = $collectionFactory;
        $this->searchResults         = $searchResults;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param ShipmentLabelInterface $shipmentLabel
     *
     * @return ShipmentLabelInterface
     * @throws CouldNotSaveException
     */
    public function save(ShipmentLabelInterface $shipmentLabel)
    {
        try {
            $shipmentLabel->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $shipmentLabel;
    }

    /**
     * @param $identifier
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $object = $this->shipmentLabelFactory->create();
        $object->load($identifier);

        if (!$object->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $identifier));
        }

        return $object;
    }

    /**
     * @param ShipmentLabelInterface $shipmentLabel
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentLabelInterface $shipmentLabel)
    {
        try {
            $shipmentLabel->delete();
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
        $collection = $this->collectionFactory->create();
        $searchResults = $this->searchResults->getCollectionItems($criteria, $collection);

        return $searchResults;
    }

    /**
     * Create a PostNL Shipment Label.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function create()
    {
        return $this->shipmentLabelFactory->create();
    }

    /**
     * Return a label that belongs to a shipment.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface[]|null|\Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getByShipment(\TIG\PostNL\Api\Data\ShipmentInterface $shipment)
    {
        return $this->getByShipmentId($shipment->getId());
    }

    /**
     * Return a label that belongs to a shipment.
     *
     * @param int $shipmentId
     *
     * @api
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]|null|\TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    public function getByShipmentId($shipmentId)
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $shipmentId);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return null;
        }

        return $list->getItems();
    }
}
