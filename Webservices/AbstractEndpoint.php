<?php

namespace TIG\PostNL\Webservices;

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;

abstract class AbstractEndpoint
{
    /** @var \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData */
    private $shipmentData;
    
    /**
     * AbstractEndpoint constructor.
     *
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData
     */
    public function __construct(
        ShipmentData $shipmentData
    ) {
        $this->shipmentData = $shipmentData;
    }
    
    /**
     * @throws \Magento\Framework\Webapi\Exception
     * @return mixed
     */
    abstract public function call();

    /**
     * @return string
     */
    abstract public function getLocation();
    
    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param $currentShipmentNumber
     *
     * @return array
     */
    public function getShipments($shipment, $currentShipmentNumber)
    {
        $shipments = [];
        $parcelCount = $shipment->getParcelCount();
        
        for ($number = $currentShipmentNumber; $number <= $parcelCount; $number++) {
            $shipments[] = $this->shipmentData->get($shipment, $number);
        }
        
        return ['Shipment' => $shipments];
    }
}
