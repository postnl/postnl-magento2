<?php

namespace TIG\PostNL\Test\Integration\Service\Volume\Shipment;

use TIG\PostNL\Service\Volume\Items\Calculate;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Config\Provider\ShippingOptions;

class CalculateTest extends TestCase
{
    public $instanceClass = Calculate::class;

    public function getDataProvider()
    {
        return [
            'Extra@Home enabled'  => [true, 50000],
            'Extra@Home disabled' => [false, 0]
        ];
    }

    /**
     * @param $enabled
     * @param $expected
     *
     * @dataProvider getDataProvider
     */
    public function testGetWithExtraAtHomeOrder($enabled, $expected)
    {
        require __DIR__.'/../../../../Fixtures/Extra_at_home/ExtraAtHomeOrder.php';

        $shippingOptions = $this->getMockBuilder(ShippingOptions::class);
        $shippingOptions->disableOriginalConstructor();
        $shippingOptions = $shippingOptions->getMock();

        $this->objectManager->configure([
            'preferences' => [
                ShippingOptions::class => get_class($shippingOptions)
            ]
        ]);

        $shippingOptions = $this->objectManager->get(ShippingOptions::class);
        $shippingOptions->method('isExtraAtHomeActive')->willReturn($enabled);

        /** @var \Magento\Sales\Model\Order $order */
        $order  = $this->getOrder();
        $result = $this->getInstance()->get($order->getItems());

        $this->assertEquals($expected, $result);
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
