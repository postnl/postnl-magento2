<?php

namespace TIG\PostNL\Test\Integration\Service\Quote;

use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Quote\Model\Quote;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Config\Provider\Webshop;

class ShippingDurationTest extends TestCase
{
    public $instanceClass = ShippingDuration::class;

    public function getDataProvider()
    {
        return  [
            'has quote' => [true, '3'], // Product fixture set on '3'.
            'has no quote' => [false, '1'] // Default system configuration fallback.
        ];
    }

    /**
     * @param $hasQuote
     * @param $expected
     *
     * @dataProvider getDataProvider
     */
    public function testGet($hasQuote, $expected)
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteShippingDuration.php';
        $quote = $this->getQuote($hasQuote);

        $checkoutSession        = $this->getFakeMock(QuoteInterface::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quote);

        $webshopConfiguration = $this->getFakeMock(Webshop::class)->getMock();
        $webshopExpects = $webshopConfiguration->expects($this->any())->method('getShippingDuration');
        $webshopExpects->willReturn('1');

        $productRepository = $this->getObject(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $productRepository->get('simple_in_stock');

        $productsMock = $this->getFakeMock(\Magento\Framework\Data\Collection\AbstractDb::class)->getMock();
        $productsExpects = $productsMock->method('getItems');
        $productsExpects->willReturn([$product]);

        $productCollection = $this->getFakeMock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class)->getMock();
        $productCollectionExpects = $productCollection->method('addFieldToFilter');
        $productCollectionExpects->willReturn($productCollection);

        $productCollectionExpects = $productCollection->method('addAttributeToSelect');
        $productCollectionExpects->willReturn($productsMock);

        $productCollectionFactory = $this->getFakeMock(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class)->getMock();
        $productCollectionFactoryExpects = $productCollectionFactory->method('create');
        $productCollectionFactoryExpects->willReturn($productCollection);

        $instance = $this->getInstance([
            'checkoutSession' => $checkoutSession,
            'webshopConfiguration' => $webshopConfiguration,
            'productCollectionFactory' => $productCollectionFactory
        ]);

        $restult = $instance->get();
        $this->assertEquals($expected, $restult);
    }

    /**
     * @param $hasQuote
     * @return Quote
     */
    private function getQuote($hasQuote)
    {
        if (!$hasQuote) {
            return null;
        }

        return $this->getObject(Quote::class)->load('shippingDuration_01', 'reserved_order_id');
    }
}
