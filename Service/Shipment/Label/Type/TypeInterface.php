<?php

namespace TIG\PostNL\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;

interface TypeInterface
{
    /**
     * @param ShipmentLabelInterface $label
     *
     * @return \FPDF
     */
    public function process(ShipmentLabelInterface $label);

    /**
     * Cleanup after we are done.
     */
    public function cleanup();
}
