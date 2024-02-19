<?php

namespace TIG\PostNL\Test\Integration\Webservices\Parser\Label;

use \TIG\PostNL\Webservices\Parser\Label\Shipments;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;
use TIG\PostNL\Helper\AddressEnhancer;

class ShipmentsTest extends TestCase
{
    public $instanceClass = Shipments::class;

    private $contactKeys = ['ContactType', 'Email', 'TelNr'];

    private $addressKeys = [
        0 => 'AddressType',
        1 => 'FirstName',
        2 => 'Name',
        3 => 'CompanyName',
        4 => 'Street',
        5 => 'HouseNr',
        6 => 'HouseNrExt',
        7 => 'Zipcode',
        8 => 'City',
        9 => 'Region',
        10 => 'Countrycode',
    ];

    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testGet()
    {
        $postNLShipment = $this->getPostNLShipment();

        $shipmentData = $this->getObject(ShipmentData::class);

        /** @var Shipments $instance */
        $instance = $this->getInstance([
            'shipmentData' => $shipmentData,
            'addressEnhancer' => $this->getAddressEnhancerMock()
        ]);

        $result = $instance->get($postNLShipment, 1);

        $this->assertIsArray($result);

        $contacts = $result['Contacts']['Contact'];
        $address  = $result['Addresses']['Address'][0];

        $this->assertEquals($this->contactKeys, array_keys($contacts));
        $this->assertEquals($this->addressKeys, array_keys($address));
    }

    /**
     * @return \TIG\PostNL\Model\Shipment
     */
    private function getPostNLShipment()
    {
        /** @var Collection $orderCollection */
        $orderCollection = $this->getObject(Collection::class);
        $orderCollection->addFieldToFilter('customer_email', 'customer@null.com');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $orderCollection->getFirstItem();

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        $shippingAddress = $shipment->getShippingAddress();
        $shippingAddress->setStreet('street 69 A');
        $shippingAddress->save();

        $factory = $this->getObject(\TIG\PostNL\Model\ShipmentFactory::class);

        /** @var \TIG\PostNL\Model\Shipment $postNLShipment */
        $postNLShipment = $factory->create();
        $postNLShipment->setOrderId($shipment->getOrderId());
        $postNLShipment->setShipmentId($shipment->getId());
        $postNLShipment->setShipmentType('Daytime');
        $postNLShipment->save();

        return $postNLShipment;
    }

    /**
     * @param \TIG\PostNL\Model\Shipment $shipment
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getProductOptionsMock($shipment)
    {
        $optionMock = $this->getFakeMock(\TIG\PostNL\Service\Shipment\ProductOptions::class, true);
        $optionMockExpects = $optionMock->expects($this->once());
        $optionMockExpects->method('get');
        $optionMockExpects->with($shipment);
        $optionMockExpects->willReturn(null);

        return $optionMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getAddressEnhancerMock()
    {
        $return  = [
            'street' => [
                'street'
            ],
            'housenumber' => '69',
            'housenumberExtension' => 'A'
        ];

        $enhancerMock = $this->getFakeMock(AddressEnhancer::class, true);
        $enhancerMockExpects = $enhancerMock->expects($this->any());
        $enhancerMockExpects->method('get');
        $enhancerMockExpects->willReturn($return);

        return $enhancerMock;
    }
}
