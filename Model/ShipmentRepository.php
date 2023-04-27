<?php

namespace TIG\PostNL\Model;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ResourceModel\Shipment\CollectionFactory;
use TIG\PostNL\Service\Filter\SearchResults;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

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
     * @var SearchResults
     */
    private $searchResults;

    /**
     * @param ShipmentFactory       $shipmentFactory
     * @param CollectionFactory     $collectionFactory
     * @param SearchResults         $searchResults
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        CollectionFactory $collectionFactory,
        SearchResults $searchResults,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentFactory       = $shipmentFactory;
        $this->collectionFactory     = $collectionFactory;
        $this->searchResults         = $searchResults;
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
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
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
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();
        $searchResults = $this->searchResults->getCollectionItems($criteria, $collection);

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
