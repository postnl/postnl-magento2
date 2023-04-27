<?php

namespace TIG\PostNL\Test\Integration\Service\Quote;

use Magento\Quote\Model\Quote;
use TIG\PostNL\Service\Quote\CheckIfQuoteItemsAreInStock;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class CheckIfQuoteItemsAreInStockTest extends TestCase
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterfaceMock;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfigurationMock;

    public $instanceClass = CheckIfQuoteItemsAreInStock::class;

    public function setUp() : void
    {
        parent::setUp();

        $this->stockRegistryInterfaceMock = $this->getFakeMock(StockRegistryInterface::class)->getMock();
        $this->stockConfigurationMock     = $this->getFakeMock(StockConfigurationInterface::class)->getMock();
    }

    public function testQuoteItemsAreInStock()
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteInStock.php';

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getObject(Quote::class)->load('instock01', 'reserved_order_id');

        $instance = $this->getInstance(
            [
                '$stockRegistryInterface' => $this->stockRegistryInterfaceMock,
                '$stockConfiguration'     => $this->stockConfigurationMock,
            ]
        );
        $result   = $instance->getValue($quote);

        $this->assertEquals(true, $result);
    }

    public function testQuoteItemsNotInStock()
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteNotInStock.php';

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getObject(Quote::class)->load('notinstock01', 'reserved_order_id');

        $quote->getItemsCollection()->getFirstItem()->setQty(101);

        $instance = $this->getInstance(
            [
                '$stockRegistryInterface' => $this->stockRegistryInterfaceMock,
                '$stockConfiguration'     => $this->stockConfigurationMock,
            ]
        );
        $result   = $instance->getValue($quote);

        $this->assertEquals(false, $result);
    }
}
