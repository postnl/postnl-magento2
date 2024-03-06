<?php

namespace TIG\PostNL\Api;

interface ShipmentBarcodeRepositoryInterface
{
    /**
     * Save a PostNL Shipment Barcode.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function save(\TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode);

    /**
     * Create a PostNL Shipment Barcode.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function create();

    /**
     * Return a specific PostNL Shipment Barcode.
     *
     * @api
     * @param int $id
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    // @codingStandardsIgnoreLine
    public function getById($id);

    /**
     * Retrieve a list of PostNL Shipment Barcodes.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific PostNL Shipment Barcode.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode);

    /**
     * Retrieve a barcode for a shipment specified by number.
     *
     * @param Data\ShipmentInterface $shipment
     * @param int                    $number
     *
     * @param                        $type
     *
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function getForShipment(\TIG\PostNL\Api\Data\ShipmentInterface $shipment, $number, $type);
}
