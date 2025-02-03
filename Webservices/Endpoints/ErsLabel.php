<?php

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Config\Provider\PrintSettingsConfiguration;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentLabelParser;
use TIG\PostNL\Webservices\Parser\Label\ErsShipment as ReturnShipmentLabel;
use TIG\PostNL\Webservices\Parser\Label\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Soap;

class ErsLabel extends ReturnLabel
{
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
            $printConfiguration,
            $returnShipmentData
        );
    }
}
