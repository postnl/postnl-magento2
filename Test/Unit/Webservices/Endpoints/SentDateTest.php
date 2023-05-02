<?php

namespace TIG\PostNL\Test\Unit\Webservices\Endpoints;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;

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
                ['country' => 'BE', 'postcode' => '1000', 'delivery_date' => '19-11-2016'],
                ['country' => 'BE', 'postcode' => '1000', 'delivery_date' => '19-11-2016'],
            ],
            'Random address DE' => [
                ['country' => 'DE', 'postcode' => '10179', 'delivery_date' => null],
                ['country' => 'NL', 'postcode' => '2132WT', 'delivery_date' => '19-11-2016'],
            ],
            'Reverse date'      => [
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
        $this->mockFunction($orderMock, 'getShippingAddress', $address);

        $fallbackMock = $this->deliveryDateFallbackMock();
        $optionsMock  = $this->optionsMock();

        $instance = $this->getInstance([
            'dateFallback'     => $fallbackMock,
            'timeframeOptions' => $optionsMock
        ]);
        $instance->updateParameters($address, 1, $orderMock);

        $result = $this->getProperty('requestParams', $instance);

        $this->assertEquals($expected['country'], $result['GetSentDate']['CountryCode']);
        $this->assertEquals($expected['postcode'], $result['GetSentDate']['PostalCode']);
        $this->assertEquals($expected['delivery_date'], $result['GetSentDate']['DeliveryDate']);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function optionsMock()
    {
        $optionsMock     = $this->getFakeMock(\TIG\PostNL\Service\Timeframe\Options::class)->getMock();
        $optionsExpected = $optionsMock->expects($this->any());
        $optionsExpected->method('get');
        $optionsExpected->willReturn(['Daytime']);

        return $optionsMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function deliveryDateFallbackMock()
    {
        $fallbackMock = $this->getFakeMock(DeliveryDateFallback::class)->getMock();
        $getFallback  = $fallbackMock->expects($this->any());
        $getFallback->method('get');
        $getFallback->willReturn('19-11-2016');

        $getFallback2 = $fallbackMock->expects($this->any());
        $getFallback2->method('getDate');
        $getFallback2->willReturn('19-11-2016');

        return $fallbackMock;
    }
}
