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

    /**
     * @return array
     */
    public function get()
    {
        $customer                       = $this->customer->get();
        $customer['Address']            = $this->customer->address();
        $customer['CollectionLocation'] = $this->customer->blsCode();

        return $customer;
    }

    /**
     * @param $storeId
     */
    public function changeCustomerStoreId($storeId)
    {
        $this->customer->changeStoreId($storeId);
    }
}
