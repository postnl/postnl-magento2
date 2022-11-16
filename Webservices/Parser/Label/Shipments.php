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

use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;

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
        $address[] = $this->getAddressData($postnlShipment->getShippingAddress());
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
            'ContactType' => '01', // Receiver
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
    private function getAddressData($shippingAddress, $addressType = '01')
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
        return ($this->returnOptions->isReturnActive() && !$this->returnOptions->isSmartReturnActive() && in_array($countryId, ['NL', 'BE']));
    }

    /**
     * @return array
     */
    private function getReturnAddressData()
    {
        $countryCode = $this->addressConfiguration->getCountry();
        $freePostNumber  = ($countryCode == 'BE' ? 'getHouseNumber' : 'getFreepostNumber');

        $data = [
            'AddressType'      => '08',
            'City'             => $this->returnOptions->getCity(),
            'CompanyName'      => $this->returnOptions->getCompany(),
            'Countrycode'      => $countryCode,
            'HouseNr'          => $this->returnOptions->$freePostNumber(),
            'Street'           => ($countryCode == 'BE' ? $this->returnOptions->getStreetName() : 'Antwoordnummer:'),
            'Zipcode'          => strtoupper(str_replace(' ', '', $this->returnOptions->getZipcode())),
        ];

        return $data;
    }
}
