<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
