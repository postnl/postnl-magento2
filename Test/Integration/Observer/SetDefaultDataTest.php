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
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Observer\TIGPostNLOrderSaveBefore\SetDefaultData;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Service\Options\ItemsToOption;
use TIG\PostNL\Service\Order\ProductCodeAndType;
use Magento\Framework\Event\Observer;

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

        $productCodeAndType = $this->getMockBuilder(ProductCodeAndType::class);
        $productCodeAndType->disableOriginalConstructor();
        $productCodeAndType = $productCodeAndType->getMock();

        $this->objectManager->configure([
            'preferences' => [
                ProductCodeAndType::class => get_class($productCodeAndType),
                ItemsToOption::class => get_class($itemsToOptions)
            ],
        ]);

        $getFromQuote = $this->objectManager->get(ItemsToOption::class);
        $getFromQuote->method('getFromQuote')->willReturn(ProductCodeAndType::OPTION_EXTRAATHOME);

        $getProductInfo = $this->objectManager->get(ProductCodeAndType::class);
        $getProductInfo->method('get')->willReturn([
            'type' => ProductCodeAndType::SHIPMENT_TYPE_EXTRAATHOME,
            'code' => 3534
        ]);

        /** @var \TIG\PostNL\Api\Data\OrderInterface $postNLOrder */
        $postNLOrder = $this->getPostNLOrder();

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('data_object', $postNLOrder);

        $this->getInstance()->execute($observer);

        $this->assertEquals(3534, $postNLOrder->getProductCode());
        $this->assertEquals(ProductCodeAndType::SHIPMENT_TYPE_EXTRAATHOME, $postNLOrder->getType());
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
            $postNLOrder->setType(ProductCodeAndType::SHIPMENT_TYPE_DAYTIME);
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