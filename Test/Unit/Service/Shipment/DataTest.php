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
namespace TIG\PostNL\Test\Unit\Service\Shipment;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Service\Shipment\Data;
use TIG\PostNL\Service\Shipment\ProductOptions;
use TIG\PostNL\Test\TestCase;

class DataTest extends TestCase
{
    public $instanceClass = Data::class;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ShipmentInterface
     */
    private $shipmentMock;

    public function setUp()
    {
        parent::setUp();

        $this->shipmentMock = $this->getMock(ShipmentInterface::class);
    }

    public function testReturnsTheDefaultData()
    {
        $address = $this->getAddress();
        $contact = $this->getContact();
        $shipmentData = $this->getShipmentData($address, $contact);

        /** @var Data $instance */
        $instance = $this->getInstance();
        $result = $instance->get($this->shipmentMock, $address, $contact);

        foreach ($shipmentData as $key => $value) {
            $this->assertEquals($value, $result[$key], $key);
            unset($shipmentData[$key]);
        }

        if (count($shipmentData)) {
            $this->fail('$shipmentData holds data but should be empty');
        }
    }

    public function testHasProductOptionsWhenApplicable()
    {
        $result = $this->addProductOptions(['a' => 'b']);

        $this->assertArrayHasKey('ProductOptions', $result);
        $this->assertEquals($result['ProductOptions'], ['a' => 'b']);
    }

    public function testHasNoProductOptionsByDefault()
    {
        $result = $this->addProductOptions(null);

        $this->assertArrayNotHasKey('ProductOptions', $result);
    }

    public function testAddsAmountWhenInsured()
    {
        $result = $this->addAmount(500);

        $expected = [
            'AccountName'       => '',
            'BIC'               => '',
            'IBAN'              => '',
            'AmountType'        => '02', // 01 = COD, 02 = Insured
            'Currency'          => 'EUR',
            'Reference'         => '',
            'TransactionNumber' => '',
            'Value'             => '500.00',
        ];

        $this->assertArrayHasKey('Amounts', $result);
        $this->assertEquals([$expected], $result['Amounts']);
    }

    public function testAddsNotAmountWhenNotInsured()
    {
        $result = $this->addAmount(0);

        $this->assertArrayNotHasKey('Amounts', $result);
    }

    public function testAddGroupWhenSingleColliShipment()
    {
        $result = $this->addParcelCount(1, 1);

        $this->assertArrayNotHasKey('Group', $result);
    }

    public function testAddGroupWhenMultiColliShipment()
    {
        $result = $this->addParcelCount(4, 3);

        $expected = [ 'Group' => [
            'GroupCount' => 4,
            'GroupSequence' => 3,
            'GroupType' => '03',
            'MainBarcode' => '',
            ]
        ];

        $this->assertArrayHasKey('Groups', $result);
        $this->assertEquals($expected, $result['Groups']);
    }

    private function getAddress()
    {
        return [
            'AddressType'      => '01',
            'FirstName'        => 'TIG',
            'Name'             => 'Support',
            'CompanyName'      => 'TIG',
            'Street'           => 'Kabelweg',
            'HouseNr'          => '37',
            'HouseNrExt'       => '',
            'Zipcode'          => '1014BA',
            'City'             => 'Amsterdam',
            'Region'           => '',
            'Countrycode'      => 'NL',
        ];
    }

    private function getContact()
    {
        return [
            'ContactType' => '01', // Receiver
            'Email'       => 'servicedesk@tig.nl',
            'TelNr'       => '0031202181000',
        ];
    }

    private function getShipmentData($addresses, $contact)
    {
        $shipmentData = [
            'Addresses'                => ['Address' => $addresses],
            'Barcode'                  => null,
            'CollectionTimeStampEnd'   => '',
            'CollectionTimeStampStart' => '',
            'Contacts'                 => ['Contact' => $contact],
            'Dimension'                => ['Weight'  => round(100)],
            'DeliveryDate'             => '19-11-2016',
            'DownPartnerID'            => '12345',
            'DownPartnerLocation'      => '112345',
            'ProductCodeDelivery'      => '3085',
        ];

        $this->mockFunction($this->shipmentMock, 'getMainBarcode', $shipmentData['Barcode']);
        $this->mockFunction($this->shipmentMock, 'getTotalWeight', $shipmentData['Dimension']['Weight']);
        $this->mockFunction($this->shipmentMock, 'getDeliveryDateFormatted', $shipmentData['DeliveryDate']);
        $this->mockFunction($this->shipmentMock, 'getPgRetailNetworkId', $shipmentData['DownPartnerID']);
        $this->mockFunction($this->shipmentMock, 'getPgLocationCode', $shipmentData['DownPartnerLocation']);
        $this->mockFunction($this->shipmentMock, 'getProductCode', $shipmentData['ProductCodeDelivery']);

        return $shipmentData;
    }

    /**
     * @param $response
     *
     * @return array
     */
    private function addProductOptions($response)
    {
        $productOptions = $this->getMock(ProductOptions::class);
        $this->mockFunction($productOptions, 'get', $response);

        $address = $this->getAddress();
        $contact = $this->getContact();

        /** @var Data $instance */
        $instance = $this->getInstance([
            'productOptions' => $productOptions,
        ]);
        $result = $instance->get($this->shipmentMock, $address, $contact);

        return $result;
    }

    private function addAmount($amount)
    {
        $this->mockFunction($this->shipmentMock, 'isExtraCover', !!$amount);
        $this->mockFunction($this->shipmentMock, 'getExtraCoverAmount', $amount);

        $address = $this->getAddress();
        $contact = $this->getContact();

        /** @var Data $instance */
        $instance = $this->getInstance();
        $result = $instance->get($this->shipmentMock, $address, $contact);

        return $result;
    }

    /**
     * @param $count
     * @param $current
     *
     * @return array
     */
    private function addParcelCount($count, $current)
    {
        $address = $this->getAddress();
        $contact = $this->getContact();
        $this->mockFunction($this->shipmentMock, 'getParcelCount', $count);

        /** @var Data $instance */
        $instance = $this->getInstance();
        $result   = $instance->get($this->shipmentMock, $address, $contact, $current);

        return $result;
    }
}
