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
use TIG\PostNL\Service\Shipment\ErsCountries;
use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class Shipments extends AbstractShipmentLabel
{
    private AddressConfiguration $addressConfiguration;
    private ShipmentData $shipmentData;
    private ReturnOptions $returnOptions;

    public function __construct(
        AddressEnhancer $addressEnhancer,
        ManagerInterface $messageManager,
        AddressConfiguration $addressConfiguration,
        ShipmentData $shipmentData,
        ReturnOptions $returnOptions
    ) {
        parent::__construct($addressEnhancer, $messageManager);
        $this->addressConfiguration = $addressConfiguration;
        $this->shipmentData = $shipmentData;
        $this->returnOptions = $returnOptions;
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
        if (!$postnlShipment->getIsSmartReturn()) {
            // Don't add additional addresses in case it's a return label
            if ($postnlOrder->getIsPakjegemak()) {
                $address[] = $this->getAddressData($postnlShipment->getPakjegemakAddress(), '09');
            }

            if ($this->canReturn($address[0]['Countrycode'], $postnlShipment)) {
                $address[] = $this->getReturnAddressData();
            }
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
     * @param $countryId
     *
     * @return bool
     */
    public function canReturn($countryId, Shipment $postnlShipment): bool
    {
        if ($postnlShipment->isBoxablePackets() || $postnlShipment->isInternationalPacket()) {
            return false;
        }
        return ($this->returnOptions->isReturnActive() && in_array($countryId, ['NL', 'BE']));
    }

    private function getReturnAddressData(): array
    {
        $countryCode = $this->addressConfiguration->getCountry();
        $returnType = $this->returnOptions->getReturnTo();

        if ($returnType === ReturnTypes::TYPE_FREE_POST && $countryCode === 'NL') {
            $zip = strtoupper(str_replace(' ', '', $this->returnOptions->getZipcode()));
            $data = [
                'AddressType' => CustomerApi::ADDRESS_TYPE_RETURN,
                'City' => $this->returnOptions->getCity(),
                'Countrycode' => $countryCode,
                'HouseNr' => $this->returnOptions->getFreepostNumber(),
                'Street' => 'Antwoordnummer',
                'Zipcode' => $zip,
                'CompanyName' => $this->returnOptions->getCompany(),
            ];
        } else {
            $zip = strtoupper(str_replace(' ', '', $this->returnOptions->getZipcodeHome()));
            $data = [
                'AddressType' => CustomerApi::ADDRESS_TYPE_RETURN,
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
