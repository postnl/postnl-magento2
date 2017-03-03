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
namespace TIG\PostNL\Test\Integration\Block\Adminhtml\Shipment\Grid;

use Magento\Framework\Event\Observer;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use TIG\PostNL\Block\Adminhtml\Shipment\Grid\ConfirmStatus;
use TIG\PostNL\Model\ShipmentFactory;
use TIG\PostNL\Observer\SalesOrderShipmentSaveAfterEvent;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ConfirmStatusTest extends TestCase
{
    protected $instanceClass = ConfirmStatus::class;

    public function getInstance(array $args = [])
    {
        if (!isset($args['context'])) {
            $contextMock = $this->getMockForAbstractClass(ContextInterface::class, [], '', false, true, true, []);
            $processor   = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
                ->disableOriginalConstructor()
                ->getMock();
            $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);

            $args['context'] = $contextMock;
        }

        return parent::getInstance($args);
    }

    public function getIsConfirmedProvider()
    {
        return [
            'not_confirmed' => [null, false],
            'confirmed' => ['2016-11-19 21:13:12', true],
        ];
    }

    /**
     * @dataProvider getIsConfirmedProvider
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testGetCellContents($confirmed_at, $expected)
    {
        $this->markTestSkipped('Should be fixed');

        $shipment = $this->getShipment();
        $shipmentId = $shipment->getId();

        $postNLShipment = $this->getPostNLShipment($shipment);
        $postNLShipment->setConfirmedAt($confirmed_at);
        $postNLShipment->save();

        /** @var ConfirmStatus $instance */
        $instance = $this->getInstance();

        $result = $this->invokeArgs('getIsConfirmed', [['tig_postnl_confirmed_at' => $confirmed_at]], $instance);
        $this->assertEquals($expected, $result);

        $result = $this->invokeArgs('getCellContents', [['tig_postnl_confirmed_at' => $confirmed_at]], $instance);
        $this->assertInstanceOf(Phrase::class, $result);
        $text = ucfirst(($expected ? '' : 'not ') . 'confirmed');
        $this->assertEquals($text, $result->getText());
    }

    /**
     * @return Order\Shipment
     */
    protected function getShipment()
    {
        /** @var OrderCollection $orderCollection */
        $orderCollection = $this->getObject(OrderCollection::class);
        $orderCollection->addFieldToFilter('customer_email', 'customer@null.com');

        /** @var Order $order */
        $order = $orderCollection->getFirstItem();

        /** @var Order\Shipment $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        return $shipment;
    }

    /**
     * @param Order\Shipment $shipment
     *
     * @return PostNLShipment
     */
    protected function getPostNLShipment(Order\Shipment $shipment)
    {
        /** @var Observer $event */
        $event = $this->getObject(Observer::class);
        $event->setData('data_object', $shipment);

        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->once());
        $callExpects->method('call');
        $callExpects->willReturn((Object)['Barcode' => '3STOTA1234567890']);

        /** @var SalesOrderShipmentSaveAfterEvent $observer */
        $observer = $this->getObject(SalesOrderShipmentSaveAfterEvent::class, ['barcode' => $barcodeMock]);
        $observer->execute($event);

        $shipmentCollection = $this->getObject(\TIG\PostNL\Model\ResourceModel\Shipment\Collection::class);
        $shipmentCollection->addFieldToFilter('shipment_id', $shipment->getId());

        return $shipmentCollection->getFirstItem();
    }
}
