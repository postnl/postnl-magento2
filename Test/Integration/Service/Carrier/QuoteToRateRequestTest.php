<?php

namespace TIG\PostNL\Test\Integration\Service\Carrier;

use Magento\Quote\Model\Quote;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Test\Integration\TestCase;

class QuoteToRateRequestTest extends TestCase
{
    public $instanceClass = QuoteToRateRequest::class;

    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var QuoteToRateRequest
     */
    private $instance;

    public function setUp() : void
    {
        parent::setUp();

        $this->quote = $this->getObject(Quote::class);
        $this->quote->load('test_order_item_with_items', 'reserved_order_id');

        /** @var \Magento\Checkout\Model\Session\Proxy $session */
        $session = $this->getObject(\Magento\Checkout\Model\Session\Proxy::class);
        $session->replaceQuote($this->quote);

        $this->instance = $this->getInstance([
            'session' => $session,
        ]);
    }

    /**
     * @magentoDataFixture Magento/Checkout/_files/quote_with_items_saved.php
     */
    public function testSetsTheRightQuantity()
    {
        $request = $this->instance->get();

        $this->assertEquals(3, $request->getPackageQty());
    }

    /**
     * @magentoDataFixture Magento/Checkout/_files/quote_with_items_saved.php
     */
    public function testSetsTheRightWeight()
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->quote->getAllItems() as $item) {
            $item->setRowWeight(100);
        }

        $request = $this->instance->get();

        $this->assertEquals(200, $request->getPackageWeight());
    }

    /**
     * @magentoDataFixture Magento/Checkout/_files/quote_with_items_saved.php
     */
    public function testItReturnsTheRightValue()
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->quote->getAllItems() as $item) {
            $item->setRowTotalInclTax($item->getQty() * 100);
        }

        $request = $this->instance->get();

        $this->assertEquals(300, $request->getPackageValue());
    }

    public function testUsesTheCorrectAddressData()
    {
        $address = $this->quote->getShippingAddress();
        $address->setStreet($street = 'Kabelweg 37');
        $address->setPostcode($postcode = '1014BA');
        $address->setCity($city = 'Amsterdam');
        $address->setRegionId($regionId = 157);
        $address->setCountryId($country = 'NL');

        $request = $this->instance->get();

        $this->assertEquals($street, $request->getDestStreet());
        $this->assertEquals($postcode, $request->getDestPostcode());
        $this->assertEquals($city, $request->getDestCity());
        $this->assertEquals($regionId, $request->getDestRegionId());
        $this->assertEquals($country, $request->getDestCountryId());
    }

    public function testRequestHasTheCorrectWebsiteId()
    {
        $store = $this->quote->getStore();
        $store->setWebsiteId(1);

        $request = $this->instance->get();

        $this->assertEquals(1, $request->getWebsiteId());
    }

    public function testRequestContainsTheCorrectSubtotal()
    {
        $this->quote->setSubtotal(300);

        $request = $this->instance->get();

        $this->assertEquals(300, $request->getOrderSubtotal());
    }
}
