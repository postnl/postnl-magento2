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
namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Service\Shipment\Barcode\Range as BarcodeRange;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;
use TIG\PostNL\Webservices\Soap;

class Barcode extends AbstractEndpoint
{
    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var string
     */
    private $version = 'v1_1';

    /**
     * @var string
     */
    private $endpoint = 'barcode';

    /**
     * @var BarcodeRange
     */
    private $barcodeRange;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var null|int
     */
    private $storeId = null;

    /**
     * @var string
     */
    private $productCode;

    /**
     * @var ReturnOptions
     */
    private $returnOptions;

    /**
     * @var AddressConfiguration
     */
    private $addressConfiguration;

    /**
     * @param \TIG\PostNL\Webservices\Soap                   $soap
     * @param \TIG\PostNL\Service\Shipment\Barcode\Range     $barcodeRange
     * @param \TIG\PostNL\Webservices\Api\Customer           $customer
     * @param \TIG\PostNL\Webservices\Api\Message            $message
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData
     * @param ReturnOptions                                  $returnOptions
     * @param AddressConfiguration                           $addressConfiguration
     */
    public function __construct(
        Soap $soap,
        BarcodeRange $barcodeRange,
        Customer $customer,
        Message $message,
        ShipmentData $shipmentData,
        ReturnOptions $returnOptions,
        AddressConfiguration $addressConfiguration
    ) {
        $this->soap                 = $soap;
        $this->barcodeRange         = $barcodeRange;
        $this->customer             = $customer;
        $this->message              = $message;
        $this->returnOptions        = $returnOptions;
        $this->addressConfiguration = $addressConfiguration;

        parent::__construct(
            $shipmentData
        );
    }

    /**
     * @param bool $shipment
     * @param bool $isReturnBarcode
     *
     * @return mixed|\stdClass
     * @throws PostNLException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function call($shipment = false, $isReturnBarcode = false)
    {
        $this->validateRequiredValues();

        $barcode = $this->barcodeRange->getByProductCode($this->productCode, $this->storeId);

        $parameters = [
            'Message'  => $this->message->get(''),
            'Customer' => $this->customer->get($shipment, $isReturnBarcode),
            'Barcode'  => [
                'Type'  => $barcode['type'],
                'Range' => $barcode['range'],
                'Serie' => $barcode['serie'],
            ],
        ];

        $parameters = $this->updateReturnParameters($parameters, $isReturnBarcode);

        return $this->soap->call($this, 'GenerateBarcode', $parameters);
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }

    /**
     * @param string $productCode
     *
     * @return $this
     */
    public function changeProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * @param int $storeId
     */
    public function updateApiKey($storeId)
    {
        $this->storeId = $storeId;
        $this->soap->updateApiKey($storeId);
        $this->customer->changeStoreId($storeId);
    }

    /**
     * @throws \TIG\PostNL\Exception
     */
    private function validateRequiredValues()
    {
        if (empty($this->productCode)) {
            // @codingStandardsIgnoreLine
            throw new PostNLException(__('Please provide the productcode first by calling setProductCode'));
        }

        if (empty($this->storeId)) {
            // @codingStandardsIgnoreLine
            throw new PostNLException(__('Please provide the store id first by calling setStoreId'));
        }
    }

    /**
     * @param $parameters
     * @param $isReturnBarcode
     * @param $sendersCountry
     *
     * @return mixed
     */
    public function updateReturnParameters($parameters, $isReturnBarcode)
    {
        if ($isReturnBarcode) {
            $parameters['Barcode']['Range'] = $this->returnOptions->getCustomerCode();
        }

        return $parameters;
    }
}
