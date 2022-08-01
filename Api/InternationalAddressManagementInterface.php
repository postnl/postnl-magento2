<?php

namespace TIG\PostNL\Api;

interface InternationalAddressManagementInterface
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


}
