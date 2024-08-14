<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;

class ShipmentLoader
{
    private array $loadedIds = [];
    /**
     * @var array
     */
    private array $shipments = [];

    /**
     * @var ShipmentRepositoryInterface
     */
    private ShipmentRepositoryInterface $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shipmentRepository    = $shipmentRepository;
    }

    /**
     * Preload all the needed models in 1 query.
     */
    public function prepareData(array $items)
    {
        $orderIds = $this->collectIds($items);
        if (empty($orderIds)) {
            return null;
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_id', $orderIds, 'IN')
            ->create();
        $models = $this->loadShipments($searchCriteria);
        foreach ($models as $model) {
            $this->shipments[$model->getOrderId()][] = $model;
        }
    }

    private function collectIds(array $items): array
    {
        $orderIds = [];

        foreach ($items as $item) {
            $id = $item['entity_id'];
            if (!isset($this->loadedIds[$id])) {
                $orderIds[] = $id;
                $this->loadedIds[$id] = 1;
            }
        }

        return $orderIds;
    }

    /**
     * @param SearchCriteria $searchCriteria
     *
     * @return ShipmentInterface[]
     */
    private function loadShipments(SearchCriteria $searchCriteria): array
    {
        /** @var SearchResults $list */
        $list = $this->shipmentRepository->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * @param int $entityId
     * @return ShipmentInterface[]
     */
    public function getShipmentsByOrderId(int $entityId): array
    {
        return $this->shipments[$entityId] ?? [];
    }
}
