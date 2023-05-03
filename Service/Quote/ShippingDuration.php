<?php

namespace TIG\PostNL\Service\Quote;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use TIG\PostNL\Service\Wrapper\QuoteInterface as CheckoutSession;
use Magento\Quote\Model\Quote as MagentoQuote;
use TIG\PostNL\Config\Provider\Webshop;
use Magento\Catalog\Api\Data\ProductInterface;

class ShippingDuration
{
    const ATTRIBUTE_CODE = 'postnl_shipping_duration';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Webshop
     */
    private $webshopConfiguration;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * ShippingDuration constructor.
     *
     * @param CheckoutSession   $checkoutSession
     * @param Webshop           $webshopConfiguration
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Webshop $webshopConfiguration,
        CollectionFactory $productCollectionFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->webshopConfiguration = $webshopConfiguration;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get()
    {
        $quote = $this->checkoutSession->getQuote();
        if (!$quote) {
            return $this->webshopConfiguration->getShippingDuration();
        }

        return $this->getProvidedByQuote($quote);
    }

    /**
     * @param MagentoQuote $quote
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProvidedByQuote($quote)
    {
        $store    = $quote->getStoreId();
        $products = $this->getProductsFromQuote($quote);

        $shippingDurations = array_map(function (ProductInterface $product) {
            if ($product->getData(static::ATTRIBUTE_CODE) === null) {
                return $this->webshopConfiguration->getShippingDuration();
            }
            return $product->getData(static::ATTRIBUTE_CODE);
        }, $products);

        $itemsDuration = $this->getItemsDuration($shippingDurations);
        if (false === $itemsDuration || !is_numeric($itemsDuration)) {
            return $this->webshopConfiguration->getShippingDuration($store);
        }

        return $itemsDuration < 0 ? 1 : round($itemsDuration, 0);
    }

    /**
     * @param $quote
     *
     * @return \Magento\Framework\DataObject[]
     */
    private function getProductsFromQuote($quote)
    {
        $productIds = [];
        foreach ($quote->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $productCollection = $this->productCollectionFactory->create();
        // @codingStandardsIgnoreStart
        $productCollection = $productCollection->addFieldToFilter('entity_id', ['in' => $productIds])
                                               ->addAttributeToSelect('postnl_shipping_duration');
        // @codingStandardsIgnoreEnd

        return $productCollection->getItems();
    }

    /**
     * @param $shippingDurations
     *
     * @return bool|mixed
     */
    private function getItemsDuration($shippingDurations)
    {
        if (empty($shippingDurations)) {
            return false;
        }

        return max($shippingDurations);
    }
}
