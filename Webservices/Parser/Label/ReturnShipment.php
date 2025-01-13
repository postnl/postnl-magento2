<?php

namespace TIG\PostNL\Webservices\Parser\Label;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Config\Source\Settings\ReturnTypes;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;
use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class ReturnShipment extends AbstractShipmentLabel
{
    private AddressConfiguration $addressConfiguration;
    private ShipmentData $shipmentData;
    private ReturnOptions $returnOptions;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        AddressEnhancer $addressEnhancer,
        ManagerInterface $messageManager,
        AddressConfiguration $addressConfiguration,
        ShipmentData $shipmentData,
        ReturnOptions $returnOptions,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($addressEnhancer, $messageManager);
        $this->addressConfiguration = $addressConfiguration;
        $this->shipmentData = $shipmentData;
        $this->returnOptions = $returnOptions;
        $this->scopeConfig = $scopeConfig;
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
        $postnlShipment->getProductCode();
        $shipment    = $postnlShipment->getShipment();
        $contact   = $this->getStoreContactData($shipment);
        $address[] = $this->getReturnAddressAsReceiver();

        $data = $this->shipmentData->get($postnlShipment, $address, $contact, $shipmentNumber);

        // Update product code to be sent to the API
        $senderCountry = $this->returnOptions->getCountry();
        $data['ProductCodeDelivery'] = $senderCountry === 'NL' ? '3250' : '4882';

        $contact = $data['Contacts']['Contact'];
        $data['Contacts'] = [
            $contact,
            $this->getCustomerContactData($shipment)
        ];
        return $data;
    }

    protected function getCustomerContactData(MagentoShipment $shipment): array
    {
        $shippingAddress = $shipment->getShippingAddress();
        $order           = $shipment->getOrder();
        return [
            'ContactType' => CustomerApi::ADDRESS_TYPE_SENDER,
            'Email'       => $order->getCustomerEmail(),
            'TelNr'       => $shippingAddress->getTelephone(),
        ];
    }

    protected function getStoreContactData(MagentoShipment $shipment): array
    {
        $storeId = $shipment->getStoreId();
        return [
            'ContactType' => CustomerApi::ADDRESS_TYPE_RECEIVER,
            'Email'       => $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE,
                $storeId
            ),
            'TelNr'       => $this->scopeConfig->getValue(
                'general/store_information/phone',
                ScopeInterface::SCOPE_STORE,
                $storeId
            ),
        ];
    }

    public function getAddressDataAsSenderAddress(Address $address): array
    {
        return $this->getAddressData($address, CustomerApi::ADDRESS_TYPE_SENDER);
    }

    public function getReturnAddressAsReceiver(): array
    {
        $address = $this->getReturnAddressData();
        $address['AddressType'] = CustomerApi::ADDRESS_TYPE_RECEIVER;
        return $address;
    }

    private function getReturnAddressData(): array
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
