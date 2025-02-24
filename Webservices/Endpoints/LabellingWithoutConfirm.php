<?php

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\PrintSettingsConfiguration;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Parser\Label\Customer;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;
use TIG\PostNL\Webservices\Soap;

// @codingStandardsIgnoreFile
class LabellingWithoutConfirm extends AbstractEndpoint
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
    private PrintSettingsConfiguration $printConfiguration;

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
     * LabellingWithoutConfirm constructor.
     *
     * @param \TIG\PostNL\Webservices\Soap                   $soap
     * @param \TIG\PostNL\Webservices\Parser\Label\Customer  $customer
     * @param \TIG\PostNL\Webservices\Api\Message            $message
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData
     * @param PrintSettingsConfiguration                     $printConfiguration
     */
    public function __construct(
        Soap $soap,
        Customer $customer,
        Message $message,
        ShipmentData $shipmentData,
        PrintSettingsConfiguration $printConfiguration
    ) {
        $this->soap = $soap;
        $this->customer = $customer;
        $this->message = $message;
        $this->printConfiguration = $printConfiguration;

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
        return $this->soap->call($this, 'GenerateLabelWithoutConfirm', $this->requestParams);
    }

    public function setParameters(ShipmentInterface $shipment, int $currentShipmentNumber = 1): void
    {
        $isSmartReturn = $shipment->getIsSmartReturn();
        $barcode = $shipment->getMainBarcode();
        if ($isSmartReturn) {
            $barcode = $shipment->getSmartReturnBarcode();
        }
        $printerType = ['Printertype' => $this->printConfiguration->getPrinterType($shipment)];
        $message = $this->message->get($barcode, $printerType);

        $this->requestParams = [
            'Message'   => $message,
            'Customer'  => $this->customer->get($shipment),
            'Shipments' => $this->getShipments($shipment, $currentShipmentNumber),
        ];
        if ($isSmartReturn) {
            $currentAddress = $this->requestParams['Customer']['Address'];
            // Switch address places
            $this->requestParams['Customer']['Address'] = $this->requestParams['Shipments']['Shipment'][0]['Addresses']['Address'][0];
            $this->requestParams['Shipments']['Shipment'][0]['Addresses']['Address'][0] = $currentAddress;
            unset($this->requestParams['Shipments']['Shipment'][0]['ReturnBarcode']);
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
