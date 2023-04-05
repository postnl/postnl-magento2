<?php

namespace TIG\PostNL\Service\Shipment;

class ShipmentService extends ShipmentServiceAbstract
{
    /**
     * @param $postNLShipment
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($postNLShipment)
    {
        $this->postnlShipmentRepository->save($postNLShipment);
    }
}
