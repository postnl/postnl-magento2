<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Webservices\Endpoints;

use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Soap;

class Labelling extends AbstractEndpoint
{
    // @codingStandardsIgnoreLine
    const PREG_MATCH_STREET = '#\A(.*?)\s+(\d+[a-zA-Z]{0,1}\s{0,1}[-]{1}\s{0,1}\d*[a-zA-Z]{0,1}|\d+[a-zA-Z-]{0,1}\d*[a-zA-Z]{0,1})#';

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
     * @param Soap     $soap
     * @param Customer $customer
     * @param Message  $message
     */
    public function __construct(
        Soap $soap,
        Customer $customer,
        Message $message
    ) {
        $this->soap = $soap;
        $this->customer = $customer;
        $this->message = $message;
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
     */
    public function setParameters($shipment)
    {
        $customer = $this->customer->get();
        $customer['Address'] = $this->customer->address();
        $customer['CollectionLocation'] = $this->customer->blsCode();

        $shipmentData = $this->getShipmentData($shipment);

        $barcode = $shipment->getMainBarcode();
        $printerType = ['Printertype' => 'GraphicFile|PDF'];
        $message = $this->message->get($barcode, $printerType);

        $this->requestParams = [
            'Message' => $message,
            'Customer' => $customer,
            'Shipments' => ['Shipment' => $shipmentData]
        ];
    }

    /**
     * @param Shipment $postnlShipment
     *
     * @return array
     */
    private function getShipmentData($postnlShipment)
    {
        $shipment = $postnlShipment->getShipment();

        $address = $this->getAddressData($shipment->getShippingAddress());
        $contact = $this->getContactData($shipment);

        $shipmentData = [
            'Addresses'                => ['Address' => [$address]],
            'Barcode'                  => $postnlShipment->getMainBarcode(),
            'CollectionTimeStampEnd'   => '',
            'CollectionTimeStampStart' => '',
            'Contacts'                 => ['Contact' => $contact],
            'Dimension'                => ['Weight'  => round($postnlShipment->getTotalWeight())],
            'DeliveryDate'             => $postnlShipment->getDeliveryDateFormatted(),
            'DownPartnerID'            => $postnlShipment->getPgRetailNetworkId(),
            'DownPartnerLocation'      => $postnlShipment->getPgLocationCode(),
            'ProductCodeDelivery'      => $postnlShipment->getProductCode(),
        ];

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
     *
     * @return array
     */
    private function getAddressData($shippingAddress)
    {
        $fullStreet = implode(' ', $shippingAddress->getStreet());
        $result = preg_match(self::PREG_MATCH_STREET, $fullStreet, $streetMatches);
        $result = preg_match(self::PREG_MATCH_HOUSENR, $streetMatches[2], $houseNrMatches);

        $addressArray = [
            'AddressType'      => '01',
            'FirstName'        => $shippingAddress->getFirstname(),
            'Name'             => $shippingAddress->getLastname(),
            'CompanyName'      => $shippingAddress->getCompany(),
            'Street'           => $streetMatches[1],
            'HouseNr'          => $houseNrMatches[1],
            'HouseNrExt'       => $houseNrMatches[2],
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
    public function getWsdlUrl()
    {
        return 'LabellingWebService/2_1/';
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }
}
