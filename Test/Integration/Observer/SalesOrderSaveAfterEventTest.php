<?php

namespace TIG\PostNL\Test\Integration\Observer;

use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Observer\SalesOrderSaveAfter\CreatePostNLOrder;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\ResourceModel\Order\Collection;

/**
 * @magentoDbIsolation enabled
 */
class SalesOrderSaveAfterEventTest extends TestCase
{
    protected $instanceClass = CreatePostNLOrder::class;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testExecute()
    {
        $this->disableEndpoint(DeliveryDate::class);
        $this->disableEndpoint(SentDate::class);

        /** @var Collection $orderCollection */
        $orderCollection = $this->getObject(Collection::class);
        $orderCollection->addFieldToFilter('customer_email', 'customer@null.com');

        /** @var Order $order */
        $order = $orderCollection->getFirstItem();
        $order->setData('shipping_method', 'tig_postnl_regular');

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('data_object', $order);

        $this->getInstance()->execute($observer);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->objectManager->create(OrderRepository::class);
        $postnlOrder = $orderRepository->getByFieldWithValue('order_id', $order->getId());

        $this->assertEquals($order->getId(), $postnlOrder->getData('order_id'));
    }
}
