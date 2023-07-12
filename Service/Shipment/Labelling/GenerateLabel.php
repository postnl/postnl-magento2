<?php

namespace TIG\PostNL\Service\Shipment\Labelling;

use TIG\PostNL\Service\Shipment\Labelling\Generate\WithoutConfirm;
use TIG\PostNL\Service\Shipment\Labelling\Generate\WithConfirm;
use TIG\PostNL\Api\Data\ShipmentInterface;

class GenerateLabel
{
    /**
     * @var WithoutConfirm
     */
    private $withoutConfirm;

    /**
     * @var WithConfirm
     */
    private $withConfirm;

    /**
     * @param WithConfirm    $withConfirm
     * @param WithoutConfirm $withoutConfirm
     */
    public function __construct(
        WithConfirm $withConfirm,
        WithoutConfirm $withoutConfirm
    ) {
        $this->withConfirm    = $withConfirm;
        $this->withoutConfirm = $withoutConfirm;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentShipmentNumber
     * @param bool              $confirm
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function get(ShipmentInterface $shipment, $currentShipmentNumber, $confirm)
    {
        if ($confirm) {
            return $this->withConfirm->get($shipment, $currentShipmentNumber);
        }

        return $this->withoutConfirm->get($shipment, $currentShipmentNumber);
    }
}
