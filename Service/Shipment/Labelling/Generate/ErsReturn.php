<?php

namespace TIG\PostNL\Service\Shipment\Labelling\Generate;

use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Webservices\Endpoints\ErsLabel;
use TIG\PostNL\Service\Shipment\Labelling\Handler;

class ErsReturn extends ReturnLabel
{
    public function __construct(
        Data $helper,
        Handler $handler,
        Log $logger,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ErsLabel $labelling
    ) {
        parent::__construct(
            $helper,
            $handler,
            $logger,
            $shipmentLabelFactory,
            $shipmentLabelRepository,
            $shipmentRepository,
            $labelling
        );
    }
}
