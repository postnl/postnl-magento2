<?php

namespace TIG\PostNL\Service\Shipment\Labelling\Generate;

use TIG\PostNL\Service\Shipment\Labelling\GenerateAbstract;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Endpoints\SingleReturnLabel;
use TIG\PostNL\Service\Shipment\Labelling\Handler;

class SingleBeReturn extends GenerateAbstract
{
    private SingleReturnLabel $labelling;

    public function __construct(
        Data $helper,
        Handler $handler,
        Log $logger,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        SingleReturnLabel $labelling
    ) {
        parent::__construct(
            $helper,
            $handler,
            $logger,
            $shipmentLabelFactory,
            $shipmentLabelRepository,
            $shipmentRepository
        );

        $this->labelling = $labelling;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentNumber
     *
     * @return null|\TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    public function get(ShipmentInterface $shipment, $currentNumber)
    {
        $this->setLabelService($this->labelling);

        return $this->getLabel($shipment, $currentNumber, false);
    }
}
