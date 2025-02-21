<?php

namespace TIG\PostNL\Webservices\Parser\Label;

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class ErsShipment extends ReturnShipment
{
    /**
     * @param Shipment $postnlShipment
     * @param          $shipmentNumber
     *
     * @return array
     * @throws \TIG\PostNL\Exception
     */
    public function get(Shipment $postnlShipment, $shipmentNumber): array
    {
        $shipment    = $postnlShipment->getShipment();
        $contact   = $this->getStoreContactData($shipment);
        $address[] = $this->getReturnAddressAsReceiver();

        $data = $this->shipmentData->get($postnlShipment, $address, $contact, $shipmentNumber);

        // Update product code to be sent to the API
        $data['ProductCodeDelivery'] = '4910';
        unset(
            $data['ProductOptions'], $data['DownPartnerID'], $data['DownPartnerLocation'], $data['DownPartnerBarcode'],
            $data['ReturnBarcode'], $data['CollectionTimeStampEnd'], $data['CollectionTimeStampStart']
        );

        $contact = $data['Contacts']['Contact'];
        $data['Contacts'] = [
            $contact,
            $this->getCustomerContactData($shipment)
        ];
        return $data;
    }

    public function getReturnAddressAsReceiver(): array
    {
        $countryCode = $this->addressConfiguration->getCountry();
        $zip = strtoupper(str_replace(' ', '', $this->returnOptions->getZipcodeHome()));
        $data = [
            'AddressType' => CustomerApi::ADDRESS_TYPE_RECEIVER,
            'City' => $this->returnOptions->getCity(),
            'Name' => $this->returnOptions->getCompany(),
            'Countrycode' => $countryCode,
            'HouseNr' => trim($this->returnOptions->getHouseNumber()),
            'HouseNrExt' => trim($this->returnOptions->getHouseNumberEx()),
            'Street' => $this->returnOptions->getStreetName(),
            'Zipcode' => $zip,
        ];

        return $data;
    }
}
