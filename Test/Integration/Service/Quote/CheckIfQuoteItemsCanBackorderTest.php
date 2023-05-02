<?php

namespace TIG\PostNL\Test\Integration\Service\Quote;

use Magento\Quote\Model\Quote;
use TIG\PostNL\Service\Quote\CheckIfQuoteItemsCanBackorder;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class CheckIfQuoteItemsCanBackorderTest extends TestCase
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterfaceMock;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfigurationMock;

    public $instanceClass = CheckIfQuoteItemsCanBackorder::class;

    public function setUp() : void
    {
        parent::setUp();

        $this->stockRegistryInterfaceMock = $this->getFakeMock(StockRegistryInterface::class)->getMock();
        $this->stockConfigurationMock     = $this->getFakeMock(StockConfigurationInterface::class)->getMock();
    }

    public function testQuoteItemsCanBackorder()
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteCanBackorder.php';

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getObject(Quote::class)->load('backorder01', 'reserved_order_id');

        $checkoutSession        = $this->getFakeMock(CheckoutSession::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quote);

        $instance = $this->getInstance(
            [
                'checkoutSession'         => $checkoutSession,
                '$stockRegistryInterface' => $this->stockRegistryInterfaceMock,
                '$stockConfiguration'     => $this->stockConfigurationMock,
            ]
        );
        $result   = $instance->getValue();

        $this->assertEquals(true, $result);
    }

    public function testQuoteItemsCanNotBackorder()
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteCanNotBackorder.php';

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getObject(Quote::class)->load('nobackorder01', 'reserved_order_id');

        $checkoutSession        = $this->getFakeMock(CheckoutSession::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quote);

        $instance = $this->getInstance(
            [
                'checkoutSession'         => $checkoutSession,
                '$stockRegistryInterface' => $this->stockRegistryInterfaceMock,
                '$stockConfiguration'     => $this->stockConfigurationMock,
            ]
        );
        $result   = $instance->getValue();

        $this->assertEquals(false, $result);
    }
}
