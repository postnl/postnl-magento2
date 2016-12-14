<?php
namespace TIG\PostNL\Api;

use TIG\PostNL\Model\ShipmentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ShipmentRepositoryInterface
{
    public function save(ShipmentInterface $shipment);

    public function getById($identifier);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(ShipmentInterface $shipment);
}
