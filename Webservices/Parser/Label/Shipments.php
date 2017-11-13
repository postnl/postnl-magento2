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
namespace TIG\PostNL\Webservices\Parser\Label;

use TIG\PostNL\Service\Shipment\Data as ShipmentData;
use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Model\Shipment;

class Shipments
{
    const PREG_MATCH_STREET = '/([^\d]+)\s?(.+)/i';

    const PREG_MATCH_HOUSENR = '#^([\d]+)(.*)#s';

    /**
     * @var ShipmentData
     */
    private $shipmentData;

    /**
     * @param ShipmentData $shipmentData
     */
    public function __construct(
        ShipmentData $shipmentData
    ) {
        $this->shipmentData = $shipmentData;
    }

    /**
     * @param Shipment $postnlShipment
     * @param          $shipmentNumber
     *
     * @return array
     */
    public function get(Shipment $postnlShipment, $shipmentNumber)
    {
        $shipment = $postnlShipment->getShipment();
        $postnlOrder = $postnlShipment->getPostNLOrder();

        $contact = $this->getContactData($shipment);
        $address[] = $this->getAddressData($postnlShipment->getShippingAddress());

        if ($postnlOrder->getIsPakjegemak()) {
            $address[] = $this->getAddressData($postnlShipment->getPakjegemakAddress(), '09');
        }

        $shipmentData = $this->shipmentData->get($postnlShipment, $address, $contact, $shipmentNumber);

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
            'HouseNr'    => isset($houseNrMatches[1]) ? trim($houseNrMatches[1]) : '',
            'HouseNrExt' => isset($houseNrMatches[2]) ? trim($houseNrMatches[2]) : '',
        ];
    }
}
