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
namespace TIG\PostNL\Test\Unit\Webservices\Endpoints;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Endpoints\SentDate;

class SentDateTest extends \TIG\PostNL\Test\TestCase
{
    public $instanceClass = SentDate::class;

    public function theParametersAreSetCorrectlyProvider()
    {
        /**
         * Today = 18-11-2016
         */

        return [
            'Random address NL' => [
                ['country' => 'NL', 'postcode' => '1014 BA', 'delivery_date' => '19-11-2016'],
                ['country' => 'NL', 'postcode' => '1014BA', 'delivery_date' => '19-11-2016'],
            ],
            'Random address BE' => [
                ['country' => 'BE', 'postcode' => '1000', 'delivery_date' => null],
                ['country' => 'NL', 'postcode' => '2132WT', 'delivery_date' => '19-11-2016'],
            ],
            'Random address DE' => [
                ['country' => 'DE', 'postcode' => '10179', 'delivery_date' => null],
                ['country' => 'NL', 'postcode' => '2132WT', 'delivery_date' => '19-11-2016'],
            ],
            'Reverse date' => [
                ['country' => 'NL', 'postcode' => '1014 BA', 'delivery_date' => '2016-11-19'],
                ['country' => 'NL', 'postcode' => '1014BA', 'delivery_date' => '19-11-2016'],
            ],
        ];
    }

    /**
     * @dataProvider theParametersAreSetCorrectlyProvider
     *
     * @param $input
     * @param $expected
     *
     * @throws \Exception
     */
    public function testTheParametersAreSetCorrectly($input, $expected)
    {
        /** @var Address $address */
        $address = $this->getObject(Address::class);
        $address->setCountryId($input['country']);
        $address->setPostcode($input['postcode']);

        $shipmentMock = $this->getFakeMock(Shipment::class, true);
        $this->mockFunction($shipmentMock, 'getShippingAddress', $address);

        $orderMock = $this->getMock(OrderInterface::class);
        $this->mockFunction($orderMock, 'getDeliveryDate', $input['delivery_date']);
        $timezoneMock = $this->timezoneInterfaceMock();

        $instance = $this->getInstance([
            'timezone' => $timezoneMock,
        ]);
        $instance->setParameters($address, 1, $orderMock);

        $result = $this->getProperty('requestParams', $instance);

        $this->assertEquals($expected['country'], $result['GetSentDate']['CountryCode']);
        $this->assertEquals($expected['postcode'], $result['GetSentDate']['PostalCode']);
        $this->assertEquals($expected['delivery_date'], $result['GetSentDate']['DeliveryDate']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function timezoneInterfaceMock()
    {
        /** @var SentDate $instance */
        $postNLHelperMock = $this->getMock(TimezoneInterface::class);

        $dateTime = new \DateTime('19-11-2016');
        $getDate = $postNLHelperMock->expects($this->once());
        $getDate->method('date');
        $getDate->willReturn($dateTime);

        return $postNLHelperMock;
    }
}
