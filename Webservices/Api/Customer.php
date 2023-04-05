<?php

namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ReturnOptions;

class Customer
{
    const ADDRESS_TYPE_SENDER = '02';

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var AddressConfiguration
     */
    private $addressConfiguration;

    /**
     * @var null|int
     */
    private $storeId = null;

    /** @var ReturnOptions  */
    private $returnOptions;

    /**
     * @param AccountConfiguration $accountConfiguration
     * @param AddressConfiguration $addressConfiguration
     * @param ReturnOptions        $returnOptions
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        AddressConfiguration $addressConfiguration,
        ReturnOptions $returnOptions
    ) {
        $this->accountConfiguration = $accountConfiguration;
        $this->addressConfiguration = $addressConfiguration;
        $this->returnOptions = $returnOptions;
    }

    /**
     * @param      $shipment
     * @param bool $isReturnBarcode
     *
     * @return array
     */
    public function get($shipment = false, $isReturnBarcode = false)
    {
        $customer = [
            'CustomerCode'   => $isReturnBarcode ? $this->getReturnCustomerCode($shipment) :
                $this->accountConfiguration->getCustomerCode($this->storeId),
            'CustomerNumber' => $this->accountConfiguration->getCustomerNumber($this->storeId),
        ];

        return $customer;
    }

    /**
     * @return mixed
     */
    public function blsCode()
    {
        return $this->accountConfiguration->getBlsCode($this->storeId);
    }

    /**
     * @return array
     */
    public function address()
    {
        $addressArray = [
            'AddressType' => self::ADDRESS_TYPE_SENDER,
            'FirstName'   => $this->addressConfiguration->getFirstname($this->storeId),
            'Name'        => $this->addressConfiguration->getLastname($this->storeId),
            'CompanyName' => $this->addressConfiguration->getCompany($this->storeId),
            'Street'      => $this->addressConfiguration->getStreetname($this->storeId),
            'HouseNr'     => $this->addressConfiguration->getHousenumber($this->storeId),
            'HouseNrExt'  => $this->addressConfiguration->getHousenumberAddition($this->storeId),
            'Zipcode'     => strtoupper(str_replace(' ', '', $this->addressConfiguration->getPostcode($this->storeId))),
            'City'        => $this->addressConfiguration->getCity($this->storeId),
            'Countrycode' => $this->addressConfiguration->getCountry(),
            'Department'  => $this->addressConfiguration->getDepartment($this->storeId),
        ];

        return $addressArray;
    }

    /**
     * @param $storeId
     */
    public function changeStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @param $shipment
     *
     * @return integer
     * @throws \TIG\PostNL\Exception
     */
    public function getReturnCustomerCode($shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();

        if (in_array($shippingAddress->getCountryId(), ['NL', 'BE'])) {
            return $this->returnOptions->getCustomerCode();
        }

        throw new \TIG\PostNL\Exception('No customer code set for Returns');
    }
}
