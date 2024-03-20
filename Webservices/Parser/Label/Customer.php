<?php

namespace TIG\PostNL\Webservices\Parser\Label;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Config\Source\Settings\ReturnTypes;
use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class Customer
{
    private CustomerApi $customer;
    private ReturnOptions $returnOptions;

    public function __construct(
        CustomerApi $customer,
        ReturnOptions $returnOptions
    ) {
        $this->customer = $customer;
        $this->returnOptions = $returnOptions;
    }

    /**
     * @throws \TIG\PostNL\Exception
     */
    public function get(ShipmentInterface $shipment): array
    {
        $customer                       = $this->customer->get();
        $customer['Address']            = $this->customer->address();
        if ($shipment->getIsSmartReturn()) {
            $returnType = $this->returnOptions->getReturnTo();
            $countryCode = $this->returnOptions->getGeneralCountry();
            // For Freepost we have another address that should be set
            if ($returnType === ReturnTypes::TYPE_FREE_POST && $countryCode === 'NL') {
                $customer['Address'] = $this->customer->getFreepostAddress();
            } else {
                $customer['Address'] = $this->customer->returnAddress();
            }
            // Replace Type as return address
            $customer['Address']['AddressType'] = CustomerApi::ADDRESS_TYPE_RECEIVER;
        }
        $customer['CollectionLocation'] = $this->customer->blsCode();

        return $customer;
    }

    public function changeCustomerStoreId(int $storeId): void
    {
        $this->customer->changeStoreId($storeId);
    }
}
