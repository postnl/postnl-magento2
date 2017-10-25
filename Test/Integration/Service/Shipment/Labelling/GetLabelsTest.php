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
use TIG\PostNL\Service\Shipment\Labelling\Generate\WithConfirm;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Service\Shipment\ConfirmLabel;

class GetLabelsTest extends TestCase
{
    public $instanceClass = GetLabels::class;

    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testALabelIsRetrievedFromTheApi()
    {
        $shipment      = $this->getPostNLShipment();
        $labellingMock = $this->getLabelWithConfirmMock($shipment, $this->once());

        $generateLabel = $this->getObject(GenerateLabel::class, [
            'withConfirm' => $labellingMock,
        ]);

        /** @var GetLabels $instance */
        $instance = $this->getInstance([
            'generateLabel' => $generateLabel,
        ]);

        $result = $instance->get($shipment->getShipmentId());

        $this->assertInternalType('array', $result);
        $this->assertEquals('random label content', base64_decode($result[0]->getLabel()));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testTheApiIsOnlyCalledOnce()
    {
        $shipment      = $this->getPostNLShipment();
        $labellingMock = $this->getLabelWithConfirmMock($shipment, $this->once());
        $confirmMock   = $this->getConfirmLabelMock($this->once());

        $generateLabel = $this->getObject(GenerateLabel::class, [
            'withConfirm' => $labellingMock,
        ]);

        /** @var GetLabels $instance */
        $instance = $this->getInstance([
            'generateLabel' => $generateLabel,
            'confirmLabel'  => $confirmMock
        ]);

        $result = $instance->get($shipment->getShipmentId());

        $this->assertInternalType('array', $result);
        $this->assertEquals('random label content', base64_decode($result[0]->getLabel()));

        $label = $this->getLabel($shipment);
        $label->save();

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
     * @param $shipment
     * @param $times
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getLabelWithConfirmMock($shipment,$times)
    {
        $labelMock = $this->getFakeMock(WithConfirm::class)->getMock();
        $callExpects = $labelMock->expects($times);
        $callExpects->method('get');
        $callExpects->willReturn([$this->getLabel($shipment)]);

        return $labelMock;
    }

    private function getConfirmLabelMock($times)
    {
        $confirmMock = $this->getFakeMock(ConfirmLabel::class)->getMock();
        $callExpects = $confirmMock->expects($times);
        $callExpects->method('confirm');

        return $confirmMock;
    }

    /**
     * @param \TIG\PostNL\Model\Shipment $shipment
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject|\TIG\PostNL\Model\ShipmentLabel
     */
    private function getLabel($shipment)
    {
        /** @var ShipmentLabelFactory $factory */
        $factory = $this->getObject(ShipmentLabelFactory::class);
        $label = $factory->create();
        $label->setParentId($shipment->getId());
        $label->setLabel(base64_encode('random label content'));
        $label->setNumber(1);
        $label->setType(ShipmentLabelInterface::BARCODE_TYPE_LABEL);

        return $label;
    }
}
