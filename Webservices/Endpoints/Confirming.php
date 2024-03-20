<?php

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Parser\Label\Customer;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;

// @codingStandardsIgnoreFile
class Confirming extends AbstractEndpoint
{
    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var string
     */
    private $version = 'v2';

    /**
     * @var string
     */
    private $endpoint = 'confirm';

    /**
     * @var array
     */
    private $requestParams;

    /**
     * Confirming constructor.
     *
     * @param \TIG\PostNL\Webservices\Soap                   $soap
     * @param \TIG\PostNL\Webservices\Parser\Label\Customer  $customer
     * @param \TIG\PostNL\Webservices\Api\Message            $message
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData
     */
    public function __construct(
        Soap $soap,
        Customer $customer,
        Message $message,
        ShipmentData $shipmentData
    ) {
        $this->soap = $soap;
        $this->customer = $customer;
        $this->message = $message;

        parent::__construct(
            $shipmentData
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function call()
    {
        return $this->soap->call($this, 'Confirming', $this->requestParams);
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param int      $currentShipmentNumber
     */
    public function setParameters($shipment, $currentShipmentNumber = 1)
    {
        $this->requestParams = [
            'Message'   => $this->message->get($shipment->getMainBarcode()),
            'Customer'  => $this->customer->get($shipment),
            'Shipments' => $this->getShipments($shipment, $currentShipmentNumber),
        ];
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }
}
