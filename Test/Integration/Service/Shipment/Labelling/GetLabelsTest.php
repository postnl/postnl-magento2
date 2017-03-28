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
namespace TIG\PostNL\Test\Integration\Service\Shipment\Labelling;

use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Service\Shipment\Labelling\GenerateLabel;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class GetLabelsTest extends TestCase
{
    public $instanceClass = GetLabels::class;

    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testALabelIsRetrievedFromTheApi()
    {
        $labellingMock = $this->getLabellingMock($this->once());

        $generateLabel = $this->getObject(GenerateLabel::class, [
            'labelling' => $labellingMock,
        ]);

        /** @var GetLabels $instance */
        $instance = $this->getInstance([
            'generateLabel' => $generateLabel,
        ]);

        $shipment = $this->getPostNLShipment();

        $result = $instance->get($shipment->getShipmentId());

        $this->assertInternalType('array', $result);
        $this->assertEquals('random label content', base64_decode($result[0]->getLabel()));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testTheApiIsOnlyCalledOnce()
    {
        $labellingMock = $this->getLabellingMock($this->once());

        $generateLabel = $this->getObject(GenerateLabel::class, [
            'labelling' => $labellingMock,
        ]);

        /** @var GetLabels $instance */
        $instance = $this->getInstance([
            'generateLabel' => $generateLabel,
        ]);

        $shipment = $this->getPostNLShipment();

        $result = $instance->get($shipment->getShipmentId());

        $this->assertInternalType('array', $result);
        $this->assertEquals('random label content', base64_decode($result[0]->getLabel()));

        $result = $instance->get($shipment->getShipmentId());

        $this->assertInternalType('array', $result);
        $this->assertEquals('random label content', base64_decode($result[0]->getLabel()));
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
     * @param $times
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getLabellingMock($times)
    {
        $response = (object)[
            'ResponseShipments' => (object)[
                'ResponseShipment' => [
                    (object)[
                        'Labels' => (object)[
                            'Label' => [
                                (object)[
                                    'Content' => 'random label content',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $labellingMock = $this->getFakeMock(Labelling::class, true);
        $callExpects = $labellingMock->expects($times);
        $callExpects->method('call');
        $callExpects->willReturn($response);

        return $labellingMock;
    }
}
