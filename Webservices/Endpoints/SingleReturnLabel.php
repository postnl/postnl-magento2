<?php

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Config\Provider\PrintSettingsConfiguration;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentLabelParser;
use TIG\PostNL\Webservices\Parser\Label\ReturnShipment as ReturnShipmentLabel;
use TIG\PostNL\Webservices\Parser\Label\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Soap;

// @codingStandardsIgnoreFile
class SingleReturnLabel extends Labelling
{
    private ReturnShipmentLabel $returnShipmentData;

    public function __construct(
        Soap                       $soap,
        Customer                   $customer,
        Message                    $message,
        ShipmentLabelParser        $shipmentData,
        PrintSettingsConfiguration $printConfiguration,
        ReturnShipmentLabel        $returnShipmentData
    ) {
        parent::__construct(
            $soap,
            $customer,
            $message,
            $shipmentData,
            $printConfiguration
        );
        $this->returnShipmentData = $returnShipmentData;
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param int                        $currentShipmentNumber
     */
    public function setParameters($shipment, $currentShipmentNumber = 1)
    {
        parent::setParameters($shipment, $currentShipmentNumber);

        // Replace customer data
        $address = $shipment->getShippingAddress();
        $this->requestParams['Customer']['Address'] = $this->returnShipmentData->getAddressDataAsSenderAddress($address);
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param int $currentShipmentNumber
     */
    public function getShipments($shipment, $currentShipmentNumber): array
    {
        $shipments = [];
        $parcelCount = $shipment->getParcelCount();

        for ($number = $currentShipmentNumber; $number <= $parcelCount; $number++) {
            $shipments[] = $this->returnShipmentData->get($shipment, $number);
        }

        return ['Shipment' => $shipments];
    }
}
