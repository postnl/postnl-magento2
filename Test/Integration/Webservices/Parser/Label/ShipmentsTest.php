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
namespace TIG\PostNL\Test\Integration\Webservices\Parser\Label;

use \TIG\PostNL\Webservices\Parser\Label\Shipments;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Service\Shipment\Data as ShipmentData;

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

        $shipmentData = $this->getObject(ShipmentData::class, [
            'productOptions' => $this->getProductOptionsMock($postNLShipment)
        ]);

        /** @var Shipments $instance */
        $instance = $this->getInstance([
            'shipmentData' => $shipmentData
        ]);

        $result = $instance->get($postNLShipment, 1);

        $this->assertInternalType('array', $result);

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

        $factory = $this->getObject(\TIG\PostNL\Model\ShipmentFactory::class);

        /** @var \TIG\PostNL\Model\Shipment $postNLShipment */
        $postNLShipment = $factory->create();
        $postNLShipment->setOrderId($shipment->getOrderId());
        $postNLShipment->setShipmentId($shipment->getId());
        $postNLShipment->save();

        return $postNLShipment;
    }

    /**
     * @param $shipment
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getProductOptionsMock($shipment)
    {
        $optionMock = $this->getFakeMock('TIG\PostNL\Service\Shipment\ProductOptions')->getMock();
        $optionMockExpects = $optionMock->expects($this->once());
        $optionMockExpects->method('get');
        $optionMockExpects->with($shipment);
        $optionMockExpects->willReturn(null);

        return $optionMock;
    }
}
