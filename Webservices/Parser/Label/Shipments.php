<?php

namespace TIG\PostNL\Webservices\Parser\Label;

use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Config\Source\Settings\ReturnTypes;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;
use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class Shipments
{
    /** @var AddressConfiguration */
    private $addressConfiguration;

    /** @var ShipmentData */
    private $shipmentData;

    /** @var AddressEnhancer */
    private $addressEnhancer;

    /** @var ManagerInterface */
    private $messageManager;

    /** @var ReturnOptions */
    private $returnOptions;

    /**
     * @param AddressConfiguration $addressConfiguration
     * @param ShipmentData         $shipmentData
     * @param AddressEnhancer      $addressEnhancer
     * @param ManagerInterface     $messageManager
     * @param ReturnOptions        $returnOptions
     */
    public function __construct(
        AddressConfiguration $addressConfiguration,
        ShipmentData $shipmentData,
        AddressEnhancer $addressEnhancer,
        ManagerInterface $messageManager,
        ReturnOptions $returnOptions
    ) {
        $this->addressConfiguration = $addressConfiguration;
        $this->shipmentData    = $shipmentData;
        $this->addressEnhancer = $addressEnhancer;
        $this->messageManager  = $messageManager;
        $this->returnOptions   = $returnOptions;
    }

    /**
     * @param Shipment $postnlShipment
     * @param          $shipmentNumber
     *
     * @return array
     * @throws \TIG\PostNL\Exception
     */
    public function get(Shipment $postnlShipment, $shipmentNumber)
    {
        $shipment    = $postnlShipment->getShipment();
        $postnlOrder = $postnlShipment->getPostNLOrder();
        $contact   = $this->getContactData($shipment);
        $addressType = CustomerApi::ADDRESS_TYPE_RECEIVER;
        if ($postnlShipment->getIsSmartReturn()) {
            $addressType = CustomerApi::ADDRESS_TYPE_SENDER;
            // And change customer to sender instead of receiver
            $contact['ContactType'] = CustomerApi::ADDRESS_TYPE_SENDER;
        }
        $address[] = $this->getAddressData($postnlShipment->getShippingAddress(), $addressType);
        if ($postnlOrder->getIsPakjegemak()) {
            $address[] = $this->getAddressData($postnlShipment->getPakjegemakAddress(), '09');
        }

        if ($this->canReturn($address[0]['Countrycode'])) {
            $address[] = $this->getReturnAddressData();
        }

        return $this->shipmentData->get($postnlShipment, $address, $contact, $shipmentNumber);
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     *
     * @return mixed
     */
    private function getContactData($shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();
        $order           = $shipment->getOrder();
        $contact = [
            'ContactType' => CustomerApi::ADDRESS_TYPE_RECEIVER,
            'Email'       => $order->getCustomerEmail(),
            'TelNr'       => $shippingAddress->getTelephone(),
        ];

        return $contact;
    }

    /**
     * @param Address $shippingAddress
     * @param string  $addressType
     *
     * @return array
     * @throws \TIG\PostNL\Exception
     */
    private function getAddressData($shippingAddress, string $addressType = CustomerApi::ADDRESS_TYPE_RECEIVER)
    {
        $streetData   = $this->getStreetData($shippingAddress);
        $houseNr = isset($streetData['housenumber']) ? $streetData['housenumber'] : $shippingAddress->getStreetLine(2);
        $houseNrExt = $shippingAddress->getStreetLine(3);
        $houseNrExt = (isset($streetData['housenumberExtension']) ? $streetData['housenumberExtension'] : $houseNrExt);
        $addressArray = [
            'AddressType' => $addressType,
            'FirstName'   => $this->getFirstName($shippingAddress),
            'Name'        => $shippingAddress->getLastname(),
            'CompanyName' => $shippingAddress->getCompany(),
            'Street'      => $streetData['street'][0],
            'HouseNr'     => $houseNr,
            'HouseNrExt'  => $houseNrExt,
            'Zipcode'     => strtoupper(str_replace(' ', '', $shippingAddress->getPostcode() ?? '')),
            'City'        => $shippingAddress->getCity(),
            'Region'      => $shippingAddress->getRegion(),
            'Countrycode' => $shippingAddress->getCountryId(),
        ];

        return $addressArray;
    }

    /**
     * @param Address $shippingAddress
     *
     * @return array
     * @throws \TIG\PostNL\Exception
     */
    private function getStreetData($shippingAddress)
    {
        $this->addressEnhancer->set([
            'street' => $shippingAddress->getStreet(),
            'country' => $shippingAddress->getCountryId()
        ]);
        $streetData = $this->addressEnhancer->get();
        if (isset($streetData['error']) && $shippingAddress->getCountryId() !== 'NL'
            && $shippingAddress->getCountryId() !== 'BE') {
            return ['street' => $shippingAddress->getStreet()];
        }

        if (isset($streetData['error'])) {
            $message = $streetData['error']['code'] . ' - ' . $streetData['error']['message'];
            $this->messageManager->addErrorMessage($message);
            return ['street' => $shippingAddress->getStreet()];
        }

        return $streetData;
    }

    /**
     * @param Address $shippingAddress
     *
     * @return string
     */
    private function getFirstName($shippingAddress)
    {
        $name = $shippingAddress->getFirstname();
        $name .= ($shippingAddress->getMiddlename() ? ' ' . $shippingAddress->getMiddlename() : '');

        return $name;
    }

    /**
     * @param $countryId
     *
     * @return bool
     */
    public function canReturn($countryId)
    {
        return ($this->returnOptions->isReturnActive() && in_array($countryId, ['NL', 'BE']));
    }

    /**
     * @return array
     */
    private function getReturnAddressData()
    {
        $countryCode = $this->addressConfiguration->getCountry();
        $returnType = $this->returnOptions->getReturnTo();
        $zip = strtoupper(str_replace(' ', '', $this->returnOptions->getZipcode()));

        if ($returnType === ReturnTypes::TYPE_FREE_POST && $countryCode === 'NL') {
            $data = [
                'AddressType' => '08',
                'City' => $this->returnOptions->getCity(),
                'Countrycode' => $countryCode,
                'HouseNr' => $this->returnOptions->getFreepostNumber(),
                'Street' => 'Antwoordnummer',
                'Zipcode' => $zip,
                'CompanyName' => $this->returnOptions->getCompany(),
            ];
        } else {
            $data = [
                'AddressType' => '08',
                'City' => $this->returnOptions->getCity(),
                'CompanyName' => $this->returnOptions->getCompany(),
                'Countrycode' => $countryCode,
                'HouseNr' => trim($this->returnOptions->getHouseNumber()),
                'HouseNrExt' => trim($this->returnOptions->getHouseNumberEx()),
                'Street' => $this->returnOptions->getStreetName(),
                'Zipcode' => $zip,
            ];
        }

        return $data;
    }
}
