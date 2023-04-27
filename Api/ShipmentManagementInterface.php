<?php

namespace TIG\PostNL\Api;

interface ShipmentManagementInterface
{
    /**
     * Confirm a PostNL shipment.
     *
     * @param int $shipmentId
     *
     * @api
     * @return string
     */
    public function confirm($shipmentId);

    /**
     * Cancel a PostNL shipment confirmation.
     *
     * @param int $shipmentId
     *
     * @api
     * @return string
     */
    public function cancelConfirm($shipmentId);

    /**
     * Generate a label for a PostNL shipment.
     *
     * @param int $shipmentId
     * @param bool $smartReturns
     *
     * @api
     * @return string
     */
    public function generateLabel($shipmentId, $smartReturns);

    /**
     * Create a PostNL shipment
     *
     * @param int      $shipmentId
     * @param int|null $productCode
     * @param int|null $colliAmount
     *
     * @api
     * @return string
     */
    public function createShipment($shipmentId, $productCode = null, $colliAmount = null);
}
