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


use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Webservices\Parser\Label\Shipments as Data;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Labelling;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Parser\Label\Customer;

class LabellingTest extends TestCase
{
    protected $instanceClass = Labelling::class;

    public function testSetParameters()
    {
        $postnlShipmentMock = $this->getFakeMock(Shipment::class)->getMock();
        $postnlShipmentExpect = $postnlShipmentMock->method('getMainBarcode');
        $postnlShipmentExpect->willReturn('theBarcode');

        $messageMock = $this->getFakeMock(Message::class)->getMock();
        $messageExpect = $messageMock->method('get');
        $messageExpect->with('theBarcode');
        $messageExpect->willReturn([
            'MessagaeId' => md5('theBarcode'),
            'MessageTimeStamp' => date('d-m-Y H:i:s')
        ]);

        $customerMock = $this->getFakeMock(Customer::class)->getMock();
        $customerExpect = $customerMock->method('get');
        $customerExpect->willReturn(['CustomerData']);

        $shipmentDataMock = $this->getFakeMock(Data::class, true);
        $shipmentDataExpect = $shipmentDataMock->expects($this->once());
        $shipmentDataExpect->method('get');
        $shipmentDataExpect->willReturn(['theShipmentData']);

        $instance = $this->getInstance([
            'shipmentData' => $shipmentDataMock,
        ]);

        $instance->setParameters($postnlShipmentMock);

        $requestParams = $this->getProperty('requestParams', $instance);

        $this->assertArrayHasKey('Message', $requestParams);
        $this->assertArrayHasKey('Customer', $requestParams);
        $this->assertArrayHasKey('Shipments', $requestParams);

        $requestParamsShipment = $requestParams['Shipments']['Shipment'];
        $this->assertInternalType('array', $requestParamsShipment);
        $this->assertEquals(['theShipmentData'], $requestParamsShipment);
    }
}
