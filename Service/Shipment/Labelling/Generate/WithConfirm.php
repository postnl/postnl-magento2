<?php

namespace TIG\PostNL\Service\Shipment\Labelling\Generate;

use TIG\PostNL\Service\Shipment\Labelling\GenerateAbstract;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Endpoints\Labelling;
use TIG\PostNL\Service\Shipment\Labelling\Handler;

class WithConfirm extends GenerateAbstract
{
    /** @var \TIG\PostNL\Webservices\Endpoints\Labelling $labelling */
    private $labelling;
    
    /**
     * WithConfirm constructor.
     *
     * @param \TIG\PostNL\Helper\Data                          $helper
     * @param \TIG\PostNL\Service\Shipment\Labelling\Handler   $handler
     * @param \TIG\PostNL\Logging\Log                          $logger
     * @param \TIG\PostNL\Model\ShipmentLabelFactory           $shipmentLabelFactory
     * @param \TIG\PostNL\Api\ShipmentLabelRepositoryInterface $shipmentLabelRepository
     * @param \TIG\PostNL\Api\ShipmentRepositoryInterface      $shipmentRepository
     * @param \TIG\PostNL\Webservices\Endpoints\Labelling      $labelling
     */
    public function __construct(
        Data $helper,
        Handler $handler,
        Log $logger,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        Labelling $labelling
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
        
        return $this->getLabel($shipment, $currentNumber, true);
    }
}
