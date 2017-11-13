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
namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Parser\Label\Customer;
use TIG\PostNL\Webservices\Api\Message;
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

    /**
     * @var string
     */
    private $version = 'v2_0';

    /**
     * @var string
     */
    private $endpoint = 'label';

    /**
     * @var array
     */
    private $requestParams;

    /**
     * @var ShipmentData
     */
    private $shipmentData;

    /**
     * @param Soap           $soap
     * @param Customer       $customer
     * @param Message        $message
     * @param ShipmentData   $shipmentData
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
        $this->shipmentData = $shipmentData;
    }

    /**
     * {@inheritdoc}
     */
    public function call()
    {
        return $this->soap->call($this, 'GenerateLabelWithoutConfirm', $this->requestParams);
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param int                        $currentShipmentNumber
     */
    public function setParameters($shipment, $currentShipmentNumber = 1)
    {
        $barcode = $shipment->getMainBarcode();
        $printerType = ['Printertype' => 'GraphicFile|PDF'];
        $message = $this->message->get($barcode, $printerType);

        $this->requestParams = [
            'Message'   => $message,
            'Customer'  => $this->customer->get(),
            'Shipments' => $this->getShipments($shipment, $currentShipmentNumber),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @param $currentShipmentNumber
     *
     * @return array
     */
    private function getShipments($shipment, $currentShipmentNumber)
    {
        $shipments = [];
        for ($number = $currentShipmentNumber; $number <= $shipment->getParcelCount(); $number++) {
            $shipments[] = $this->shipmentData->get($shipment, $number);
        }

        return ['Shipment' => $shipments];
    }
}
