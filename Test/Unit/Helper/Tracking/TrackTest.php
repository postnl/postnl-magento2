<?php

namespace TIG\PostNL\Test\Unit\Helper;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;
use Magento\Store\Model\Store;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentRepository;
use TIG\PostNL\Test\TestCase;

class TrackTest extends TestCase
{
    protected $instanceClass = Track::class;

    public function testSend()
    {
        $searchCriteriaMock = $this->getFakeMock(SearchCriteria::class)->getMock();

        $searchCriteriaBuilderMock = $this->getFakeMock(SearchCriteriaBuilder::class)
            ->setMethods(['create', 'addFilter'])
            ->getMock();
        $searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteriaMock);
        $searchCriteriaBuilderMock->expects($this->once())->method('addFilter')->willReturnSelf();

        $postnlShipmentMock = $this->getFakeMock(Shipment::class)->getMock();

        $shipmentRepositoryMock = $this->getFakeMock(ShipmentRepository::class)
            ->setMethods(['getList', 'getItems'])
            ->getMock();
        $shipmentRepositoryMock->expects($this->once())->method('getList')->willReturnSelf();
        $shipmentRepositoryMock->expects($this->once())->method('getItems')->willReturn([$postnlShipmentMock]);

        $shipmentMock = $this->getFakeMock(MagentoShipment::class)->getMock();

        $instance = $this->getInstance([
            'searchCriteriaBuilder' => $searchCriteriaBuilderMock,
            'postNLShipmentRepository' => $shipmentRepositoryMock
        ]);
        $instance->send($shipmentMock);
    }

    public function generatesTheCorrectTTUrlProvider()
    {
        return [
            ['NL', 'D=NL&P=1234AB&T=C&L=NL'],
            ['BE', 'D=BE&P=1234AB&T=C&L=NL'],
        ];
    }

    /**
     * @param $country
     * @param $expected
     *
     * @dataProvider generatesTheCorrectTTUrlProvider
     */
    public function testGeneratesTheCorrectTTUrl($country, $expected)
    {
        $type = 'C';
        $barcode = '123ABC';
        $isReturn = false;
        $returnCountry = 'NL';

        /** @var OrderAddressInterface $address */
        $address = $this->getObject(Address::class);
        $address->setCountryId($country);
        $address->setPostcode('1234AB');

        $storeMock = $this->getFakeMock(Store::class, true);
        $expectsCode = $storeMock->expects($this->any());
        $expectsCode->method('getCode');
        $expectsCode->willReturn('default');

        $orderMock = $this->getFakeMock(Order::class, true);
        $expectsStore = $orderMock->expects($this->once());
        $expectsStore->method('getStore');
        $expectsStore->willReturn($storeMock);

        $address->setOrder($orderMock);

        $scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class, true);
        $expectsValue = $scopeConfigMock->expects($this->once());
        $expectsValue->method('getValue');
        $expectsValue->willReturn('nl_NL');

        $instance = $this->getInstance([
            'scopeConfig' => $scopeConfigMock,
        ]);

        $result = $this->invokeArgs('generateTrackAndTraceUrl', [$address, $barcode, $type, $isReturn, $returnCountry], $instance);

        $this->assertStringContainsString('B=123ABC&' . $expected, $result);
    }
}
