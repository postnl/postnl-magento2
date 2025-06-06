<?php

namespace TIG\PostNL\Service\Shipment\Labelling\Generate;

use TIG\PostNL\Service\Shipment\Labelling\GenerateAbstract;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Service\Shipment\Labelling\Handler;

class ReturnLabel extends GenerateAbstract
{
    protected AbstractEndpoint $labelling;

    public function __construct(
        Data $helper,
        Handler $handler,
        Log $logger,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        AbstractEndpoint $labelling
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

    /**
     * @param $labelItem
     * @param ShipmentInterface $shipment
     * @param $currentShipmentNumber
     * @param string $fileFormat
     *
     * @return array
     */
    protected function getLabelModels($labelItem, ShipmentInterface $shipment, $currentShipmentNumber, string $fileFormat)
    {
        $labelModels = [];
        $labelItemHandle = $this->handler->handle($shipment, $labelItem->Labels->Label);

        foreach ($labelItemHandle['labels'] as $Label) {
            $labelModel    = $this->save(
                $shipment,
                $currentShipmentNumber,
                $this->getLabelContent($Label),
                $labelItemHandle['type'],
                $labelItem->ProductCodeDelivery,
                $this->getLabelType($Label, $labelItem->ProductCodeDelivery),
                $fileFormat
            );
            $labelModels[] = $labelModel;
        }

        return $labelModels;
    }
}
