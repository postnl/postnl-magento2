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
