<?php

namespace TIG\PostNL\Webservices\Parser\Label;

use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class Customer
{
    /**
     * @var CustomerApi
     */
    private $customer;

    /**
     * @param CustomerApi $customer
     */
    public function __construct(
        CustomerApi $customer
    ) {
        $this->customer = $customer;
    }

    public function get(): array
    {
        $customer                       = $this->customer->get();
        $customer['Address']            = $this->customer->address();
        $customer['CollectionLocation'] = $this->customer->blsCode();

        return $customer;
    }

    public function changeCustomerStoreId(int $storeId)
    {
        $this->customer->changeStoreId($storeId);
    }

    public function getReturnAddress(): array
    {
        return $this->customer->returnAddress();
    }
}
