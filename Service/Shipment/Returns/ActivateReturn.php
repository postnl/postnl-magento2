<?php

namespace TIG\PostNL\Service\Shipment\Returns;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Webservices\Endpoints\Shipment\ActivateReturn as ActivateEndpoint;

class ActivateReturn
{
    private ActivateEndpoint $activateAction;
    private ShipmentRepositoryInterface $shipmentRepository;
    private AccountConfiguration $accountConfiguration;

    public function __construct(
        ActivateEndpoint $activateAction,
        ShipmentRepositoryInterface $shipmentRepository,
        AccountConfiguration $accountConfiguration
    ) {
        $this->activateAction = $activateAction;
        $this->shipmentRepository = $shipmentRepository;
        $this->accountConfiguration = $accountConfiguration;
    }

    public function processsShipment(ShipmentInterface $shipment): bool
    {
        // Check if return was blocked previously
        if ($shipment->getReturnStatus() !== $shipment::RETURN_STATUS_BLOCKED) {
            return false;
        }

        $data = [
            "CustomerNumber" => $this->accountConfiguration->getCustomerNumber(),
            "CustomerCode" => $this->accountConfiguration->getCustomerCode(),
            "Barcode" => $shipment->getBarcode()
        ];

        $this->activateAction->updateRequestData($data);
        $response = $this->activateAction->call();
        // Validate response and update shipment.
        if ($response && $response['successFulBarcodes']) {
            $shipment->setReturnStatus($shipment::RETURN_STATUS_RELEASED);
            $this->shipmentRepository->save($shipment);
        }
    }
}
