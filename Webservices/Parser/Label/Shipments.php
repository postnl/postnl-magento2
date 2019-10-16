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
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;

class Shipments
{
    /**
     * @var ShipmentData
     */
    private $shipmentData;

    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /** @var ReturnOptions */
    private $returnOptions;

    /**
     * @param ShipmentData     $shipmentData
     * @param AddressEnhancer  $addressEnhancer
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ShipmentData $shipmentData,
        AddressEnhancer $addressEnhancer,
        ManagerInterface $messageManager,
        ReturnOptions $returnOptions
    ) {
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

        $order = $shipment->getOrder();
        $shippingAddress = $order->getShippingAddress();

        if ($this->canReturnNl() || $this->canReturnBe()) {
            $address[] = $this->getCorrectReturnAddress($shippingAddress->getCountryId());
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
            'Zipcode'     => strtoupper(str_replace(' ', '', $shippingAddress->getPostcode())),
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
        $this->addressEnhancer->set(['street' => $shippingAddress->getStreet()]);
        $streetData = $this->addressEnhancer->get();

        if (isset($streetData['error'])) {
            $this->messageManager->addWarningMessage(
                $streetData['error']['code'] . ' - ' . $streetData['error']['message']
            );
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
        if ($shippingAddress->getMiddlename()) {
            $name .= ' ' . $shippingAddress->getMiddlename();
        }

        return $name;
    }

    /**
     * @return bool
     */
    public function canReturnNl()
    {
        return $this->returnOptions->isReturnNlActive();
    }

    /**
     * @return bool
     */
    public function canReturnBe()
    {
        return $this->returnOptions->isReturnBeActive();
    }

    /**
     * @param $countryCode
     *
     * @return array
     */
    private function getCorrectReturnAddress($countryCode)
    {
        if ($countryCode == 'NL' || $countryCode == 'BE') {
            return $this->getReturnAddress($countryCode);
        }
    }

    private function getReturnAddress($countryCode)
    {
        $city = 'getCity' . $countryCode;
        $company = 'getCompany' . $countryCode;
        $houseNo = 'getHouseNumber' . $countryCode;
        $street  = 'getFreepostNumber' . $countryCode;
        $zipcode = 'getZipcode' . $countryCode;

        $data = [
            'AddressType'      => '08',
            'City'             => $this->returnOptions->$city(),
            'CompanyName'      => $this->returnOptions->$company(),
            'Countrycode'      => $countryCode,
            'HouseNr'          => $this->returnOptions->$houseNo(),
            'Street'           => $this->returnOptions->$street(),
            'Zipcode'          => $this->returnOptions->$zipcode(),
        ];

        return $data;
    }
}
