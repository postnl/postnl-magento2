<?php

namespace TIG\PostNL\Api;

interface ShipmentRepositoryInterface
{
    /**
     * Update a PostNL shipment.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function save(\TIG\PostNL\Api\Data\ShipmentInterface $shipment);

    /**
     * Create a PostNL shipment.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function create();

    /**
     * Retrieve a specific PostNL shipment.
     *
     * @api
     * @param int $id
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    // @codingStandardsIgnoreLine
    public function getById($id);

    /**
     * Find one row by field with specific value.
     *
     * @api
     * @param string $field
     * @param string|int $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface|null
     */
    public function getByFieldWithValue($field, $value);

    /**
     * Retrieve a specific PostNL shipment by the Magento Shipment ID.
     *
     * @param int $id
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    // @codingStandardsIgnoreLine
    public function getByShipmentId($id);

    /**
     * Retrieve a list of PostNL shipments.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a PostNL order.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\ShipmentInterface $shipment);

    /**
     * Delete a PostNL shipment.
     *
     * @api
     * @param $id
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function deleteById($id);
}
