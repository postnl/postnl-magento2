<?php

namespace TIG\PostNL\Api;

interface ShipmentLabelRepositoryInterface
{
    /**
     * Save a PostNL Shipment Label.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function save(\TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel);

    /**
     * Create a PostNL Shipment Label.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function create();

    /**
     * Return a specific PostNL Shipment Label.
     *
     * @api
     * @param int $id
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    // @codingStandardsIgnoreLine
    public function getById($id);

    /**
     * Return a label that belongs to a shipment.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function getByShipment(\TIG\PostNL\Api\Data\ShipmentInterface $shipment);

    /**
     * Return a label that belongs to a PostNL shipment.
     * @api
     * @param int $shipmentId
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    public function getByShipmentId($shipmentId);

    /**
     * Retrieve a list of PostNL Shipment Labels.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \TIG\PostNL\Api\Data\ShipmentLabelSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific PostNL Shipment Label.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel);
}
