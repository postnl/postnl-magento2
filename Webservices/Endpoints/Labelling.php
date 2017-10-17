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

use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Soap;

// @codingStandardsIgnoreFile
class Labelling extends AbstractEndpoint
{
    const PREG_MATCH_STREET = '/([^\d]+)\s?(.+)/i';

    const PREG_MATCH_HOUSENR = '#^([\d]+)(.*)#s';

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
    private $version = 'v2_1';

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
        return $this->soap->call($this, 'GenerateLabel', $this->requestParams);
    }

    /**
     * @param Shipment $shipment
     * @param int      $currentShipmentNumber
     */
    public function setParameters($shipment, $currentShipmentNumber = 1)
    {
        $storeId = $shipment->getShipment()->getStoreId();
        $this->soap->updateApiKey($storeId);
        $this->customer->setStoreId($storeId);

        $customer = $this->customer->get();
        $customer['Address'] = $this->customer->address();
        $customer['CollectionLocation'] = $this->customer->blsCode();

        $shipmentData = $this->getShipmentData($shipment, $currentShipmentNumber);

        $barcode = $shipment->getMainBarcode();
        $printerType = ['Printertype' => 'GraphicFile|PDF'];
        $message = $this->message->get($barcode, $printerType);

        $this->requestParams = [
            'Message' => $message,
            'Customer' => $customer,
            'Shipments' => ['Shipment' => $shipmentData],
        ];
    }

    /**
     * @param Shipment $postnlShipment
     * @param          $currentShipmentNumber
     *
     * @return array
     */
    private function getShipmentData($postnlShipment, $currentShipmentNumber)
    {
        $shipment = $postnlShipment->getShipment();
        $postnlOrder = $postnlShipment->getPostNLOrder();

        $contact = $this->getContactData($shipment);
        $address[] = $this->getAddressData($postnlShipment->getShippingAddress());

        if ($postnlOrder->getIsPakjegemak()) {
            $address[] = $this->getAddressData($postnlShipment->getPakjegemakAddress(), '09');
        }

        $shipmentData = $this->shipmentData->get($postnlShipment, $address, $contact, $currentShipmentNumber);

        return $shipmentData;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     *
     * @return mixed
     */
    private function getContactData($shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();
        $order = $shipment->getOrder();

        $contact = [
            'ContactType' => '01', // Receiver
            'Email'       => $order->getCustomerEmail(),
            'TelNr'       => $shippingAddress->getTelephone(),
        ];

        return $contact;
    }

    /**
     * @param Address $shippingAddress
     * @param string   $addressType
     *
     * @return array
     */
    private function getAddressData($shippingAddress, $addressType = '01')
    {
        $streetData = $this->getStreet($shippingAddress);

        $addressArray = [
            'AddressType'      => $addressType,
            'FirstName'        => $shippingAddress->getFirstname(),
            'Name'             => $shippingAddress->getLastname(),
            'CompanyName'      => $shippingAddress->getCompany(),
            'Street'           => $streetData['Street'],
            'HouseNr'          => $streetData['HouseNr'],
            'HouseNrExt'       => $streetData['HouseNrExt'],
            'Zipcode'          => strtoupper(str_replace(' ', '', $shippingAddress->getPostcode())),
            'City'             => $shippingAddress->getCity(),
            'Region'           => $shippingAddress->getRegion(),
            'Countrycode'      => $shippingAddress->getCountryId(),
        ];

        return $addressArray;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }

    /**
     * @param Address $shippingAddress
     *
     * @return array
     */
    private function getStreet($shippingAddress)
    {
        $street = $shippingAddress->getStreet();
        $fullStreet = implode(' ', $street);

        if (empty($fullStreet)) {
            return [
                'Street'     => '',
                'HouseNr'    => '',
                'HouseNrExt' => '',
            ];
        }

        preg_match(self::PREG_MATCH_STREET, $fullStreet, $streetMatches);
        preg_match(self::PREG_MATCH_HOUSENR, $streetMatches[2], $houseNrMatches);

        return [
            'Street'     => trim($streetMatches[1]),
            'HouseNr'    => trim($houseNrMatches[1]),
            'HouseNrExt' => trim($houseNrMatches[2]),
        ];
    }
}
