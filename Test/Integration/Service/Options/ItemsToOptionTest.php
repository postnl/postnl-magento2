<?php

namespace TIG\PostNL\Test\Integration\Service\Options;

use TIG\PostNL\Test\Integration\TestCase;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Service\Options\ItemsToOption;
use TIG\PostNL\Service\Order\ProductInfo;

class ItemsToOptionTest extends TestCase
{
    public $instanceClass = ItemsToOption::class;

    public function testGetWithExtraAtHomeOrder()
    {
        require __DIR__.'/../../../Fixtures/Extra_at_home/ExtraAtHomeOrder.php';

        /** @var \Magento\Sales\Model\Order $order */
        $order  = $this->getOrder();
        $result = $this->getInstance()->get($order->getItems());

        $this->assertEquals(ProductInfo::OPTION_EXTRAATHOME, $result);
    }

    public function testReqularOrder()
    {
        require __DIR__.'/../../../Fixtures/Extra_at_home/NonExtraAtHomeOrder.php';

        /** @var \Magento\Sales\Model\Order $order */
        $order  = $this->getOrder();
        $result = $this->getInstance()->get($order->getItems());

        $this->assertEquals('', $result);
    }

    /**
     * @return \Magento\Framework\DataObject|\Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        /** @var Collection $orderCollection */
        $orderCollection = $this->getObject(Collection::class);
        $orderCollection->addFieldToFilter('customer_email', 'customer@tig.nl');

        return $orderCollection->getFirstItem();
    }
}
