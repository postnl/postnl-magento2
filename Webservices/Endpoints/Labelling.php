<?php

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Parser\Label\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Soap;

// @codingStandardsIgnoreFile
class Labelling extends AbstractEndpoint
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
    private $version = 'v2_2';

    /**
     * @var string
     */
    private $endpoint = 'label';

    /**
     * @var array
     */
    private $requestParams;

    /**
     * Labelling constructor.
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
        $this->soap     = $soap;
        $this->customer = $customer;
        $this->message  = $message;

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
        return $this->soap->call($this, 'GenerateLabel', $this->requestParams);
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param int                        $currentShipmentNumber
     */
    public function setParameters($shipment, $currentShipmentNumber = 1)
    {
        $storeId = $shipment->getShipment()->getStoreId();
        $this->soap->updateApiKey($storeId);
        $this->customer->changeCustomerStoreId($storeId);

        $barcode     = $shipment->getMainBarcode();
        $printerType = ['Printertype' => 'GraphicFile|PDF'];
        $message     = $this->message->get($barcode, $printerType);

        $this->requestParams = [
            'Message'   => $message,
            'Customer'  => $this->customer->get(),
            'Shipments' => $this->getShipments($shipment, $currentShipmentNumber),
        ];
        if ($shipment->getIsSmartReturn()) {
            // Reverse original address to be the receiver type
            $this->requestParams['Customer']['Address']['AddressType'] = \TIG\PostNL\Webservices\Api\Customer::ADDRESS_TYPE_RECEIVER;
        }
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }
}
