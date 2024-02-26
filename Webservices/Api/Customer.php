<?php

namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Exception;

class Customer
{
    const ADDRESS_TYPE_RECEIVER = '01';
    const ADDRESS_TYPE_SENDER = '02';

    private AccountConfiguration $accountConfiguration;

    private AddressConfiguration $addressConfiguration;

    private ReturnOptions $returnOptions;

    private ?int $storeId = null;


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
     * @throws Exception
     */
    public function get(ShipmentInterface $shipment = null, bool $isReturnBarcode = false): array
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

    public function address(): array
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

    public function returnAddress(): array
    {
        return [
            'AddressType' => self::ADDRESS_TYPE_RECEIVER,
            'FirstName'   => $this->addressConfiguration->getFirstname($this->storeId),
            'Name'        => $this->addressConfiguration->getLastname($this->storeId),
            'CompanyName' => $this->returnOptions->getCompany(),
            'Street'      => $this->returnOptions->getStreetname(),
            'HouseNr'     => $this->returnOptions->getHousenumber(),
            'HouseNrExt'  => '',
            'Zipcode'     => strtoupper(str_replace(' ', '', $this->returnOptions->getZipcode())),
            'City'        => $this->returnOptions->getCity(),
            'Countrycode' => $this->addressConfiguration->getCountry(),
            'Department'  => '',
        ];
    }

    public function changeStoreId(int $storeId): void
    {
        $this->storeId = $storeId;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return integer
     * @throws \TIG\PostNL\Exception
     */
    public function getReturnCustomerCode(ShipmentInterface $shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();

        if (in_array($shippingAddress->getCountryId(), ['NL', 'BE'])) {
            return $this->returnOptions->getCustomerCode();
        }

        throw new \TIG\PostNL\Exception('No customer code set for Returns');
    }
}
