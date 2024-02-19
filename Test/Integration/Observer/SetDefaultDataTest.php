<?php

namespace TIG\PostNL\Test\Integration\Observer;

use TIG\PostNL\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Observer\TIGPostNLOrderSaveBefore\SetDefaultData;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Service\Options\ItemsToOption;
use TIG\PostNL\Service\Order\ProductInfo;
use Magento\Framework\Event\Observer;
use TIG\PostNL\Service\Order\MagentoOrder;
use TIG\PostNL\Service\Quote\ShippingDuration;
use Magento\Sales\Model\Order\Address;
/**
 * @magentoDbIsolation enabled
 */
class SetDefaultDataTest extends TestCase
{
    protected $instanceClass = SetDefaultData::class;

    /**
     * When a customer is canceled the order in the checkout, the PostNL DB Record keeps his records.
     * Also the same quote will be used, so if the customer changes his product from regular to an Extra at Home
     * product the DB record of postnl_order could still have the product code given by the regular product type.
     * This is because after selecting one of the delivery options the record is created.
     *
     * So in this test we will first create an Non-ExtraAtHome order to simulate the checkout cancle process.
     * Where the order will have a reqular product type, the quote will maintain a Extra At Home product.
     *
     */
    public function testExecute()
    {
        require __DIR__.'/../../Fixtures/Extra_at_home/NonExtraAtHomeOrder.php';

        $this->disableEndpoint(DeliveryDate::class);
        $this->disableEndpoint(SentDate::class);

        $itemsToOptions = $this->getMockBuilder(ItemsToOption::class);
        $itemsToOptions->disableOriginalConstructor();
        $itemsToOptions = $itemsToOptions->getMock();

        $productInfo = $this->getMockBuilder(ProductInfo::class);
        $productInfo->disableOriginalConstructor();
        $productInfo = $productInfo->getMock();

        $magentoOrderService = $this->getMockBuilder(MagentoOrder::class);
        $magentoOrderService->disableOriginalConstructor();
        $magentoOrderService = $magentoOrderService->getMock();

        $shippingDuration = $this->getMockBuilder(ShippingDuration::class);
        $shippingDuration->disableOriginalConstructor();
        $shippingDuration = $shippingDuration->getMock();

        $this->objectManager->configure([
            'preferences' => [
                ProductInfo::class      => get_class($productInfo),
                ItemsToOption::class    => get_class($itemsToOptions),
                MagentoOrder::class     => get_class($magentoOrderService),
                ShippingDuration::class => get_class($shippingDuration)
            ],
        ]);

        $magentoService = $this->objectManager->get(MagentoOrder::class);
        $magentoService->method('getCountry')->willReturn('NL');

        $address = $this->getObject(Address::class);
        $address->setCountryId('NL');

        $magentoServiceGetAddress = $this->objectManager->get(MagentoOrder::class);
        $magentoServiceGetAddress->method('getShippingAddress')->willReturn($address);

        $getFromQuote = $this->objectManager->get(ItemsToOption::class);
        $getFromQuote->method('getFromQuote')->willReturn(ProductInfo::OPTION_EXTRAATHOME);

        $getProductInfo = $this->objectManager->get(ProductInfo::class);
        $getProductInfo->method('get')->willReturn([
            'type' => ProductInfo::SHIPMENT_TYPE_EXTRAATHOME,
            'code' => 3085
        ]);

        $shippingDurationExpects = $this->objectManager->get(ShippingDuration::class);
        $shippingDurationExpects->method('get')->willReturn('1');

        /** @var \TIG\PostNL\Api\Data\OrderInterface $postNLOrder */
        $postNLOrder = $this->getPostNLOrder();

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('data_object', $postNLOrder);

        $this->getInstance()->execute($observer);

        $this->assertEquals(3085, $postNLOrder->getProductCode());
        $this->assertEquals(ProductInfo::SHIPMENT_TYPE_DAYTIME, $postNLOrder->getType());
    }

    /**
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    private function getPostNLOrder()
    {
        $magentoOrder    = $this->getOrder();
        $orderRepository = $this->objectManager->create(OrderRepository::class);
        /** @var \TIG\PostNL\Api\Data\OrderInterface $postNLOrder */
        $postNLOrder = $orderRepository->getByOrderId($magentoOrder->getId());
        if (!$postNLOrder) {
            $postNLOrder = $orderRepository->create();
            $postNLOrder->setProductCode(3085);
            $postNLOrder->setType(ProductInfo::SHIPMENT_TYPE_DAYTIME);
        }

        $postNLOrder->setDeliveryDate('2017-11-06 01:00:00');
        $postNLOrder->setShipAt('2017-11-05');

        return $postNLOrder;
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
