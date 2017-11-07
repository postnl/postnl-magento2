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
namespace TIG\PostNL\Test\Unit\Helper;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;
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
            ['NL', 'D=NL&P=1234AB&T=C'],
            ['BE', 'D=BE&P=1234AB&T=C'],
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

        /** @var OrderAddressInterface $address */
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($country);
        $address->setPostcode('1234AB');

        $instance = $this->getInstance();
        $result = $this->invokeArgs('generateTrackAndTraceUrl', [$address, $barcode, $type], $instance);

        $this->assertContains('B=123ABC&' . $expected, $result);
    }
}
