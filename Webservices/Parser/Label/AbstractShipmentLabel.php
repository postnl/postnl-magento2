<?php

namespace TIG\PostNL\Webservices\Parser\Label;

use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class AbstractShipmentLabel
{
    protected AddressEnhancer $addressEnhancer;
    protected ManagerInterface $messageManager;

    public function __construct(
        AddressEnhancer $addressEnhancer,
        ManagerInterface $messageManager
    ) {
        $this->addressEnhancer = $addressEnhancer;
        $this->messageManager = $messageManager;
    }

    /**
     * @throws Exception
     */
    protected function getAddressData(Address $shippingAddress, string $addressType = CustomerApi::ADDRESS_TYPE_RECEIVER): array
    {
        $streetData = $this->getStreetData($shippingAddress);
        $houseNr = $streetData['housenumber'] ?? $shippingAddress->getStreetLine(2);
        $houseNrExt = $shippingAddress->getStreetLine(3);
        $houseNrExt = ($streetData['housenumberExtension'] ?? $houseNrExt);
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
     * @throws Exception
     */
    protected function getStreetData($shippingAddress): array
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
            $this->messageManager->addErrorMessage(
                $streetData['error']['code'] . ' - ' . $streetData['error']['message']
            );
            return ['street' => $shippingAddress->getStreet()];
        }

        return $streetData;
    }

    protected function getFirstName(Address $shippingAddress): string
    {
        $name = $shippingAddress->getFirstname();
        $name .= ($shippingAddress->getMiddlename() ? ' ' . $shippingAddress->getMiddlename() : '');

        return $name;
    }
}
