<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
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

class ConfirmStatusTest extends TestCase
{
    protected $instanceClass = ConfirmStatus::class;

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
        $shipment = $this->getShipment();
        $postNLShipment = $this->getPostNLShipment($shipment);
        $postNLShipment->setConfirmedAt($confirmed_at);
        $postNLShipment->save();

        /** @var ConfirmStatus $instance */
        $instance = $this->getFakeMock($this->instanceClass)->setMethods(null)->getMock();
        $this->setProperty('shipmentFactory', $this->getObject(ShipmentFactory::class), $instance);

        $shipmentId = $shipment->getId();
        $instance->prepareDataSource([
            'data' => [
                'items' => [
                    ['entity_id' => $shipmentId],
                ]
            ]
        ]);

        $instance->prepareData();
        $result = $this->invokeArgs('getIsConfirmed', [$shipmentId], $instance);
        $this->assertEquals($expected, $result);

        $result = $this->invokeArgs('getCellContents', [['entity_id' => $shipmentId]], $instance);
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

        /** @var SalesOrderShipmentSaveAfterEvent $observer */
        $observer = $this->getObject(SalesOrderShipmentSaveAfterEvent::class);
        $observer->execute($event);

        $shipmentCollection = $this->getObject(\TIG\PostNL\Model\ResourceModel\Shipment\Collection::class);
        $shipmentCollection->addFieldToFilter('shipment_id', $shipment->getId());

        return $shipmentCollection->getFirstItem();
    }
}
