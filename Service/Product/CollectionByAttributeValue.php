<?php

namespace TIG\PostNL\Service\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;

class CollectionByAttributeValue
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CollectionByItems
     */
    private $collectionByItems;

    /**
     * @param ProductRepository        $productRepository
     * @param CollectionByItems        $collectionByItems
     */
    public function __construct(
        ProductRepository $productRepository,
        CollectionByItems $collectionByItems
    ) {
        $this->productRepository = $productRepository;
        $this->collectionByItems = $collectionByItems;
    }

    /**
     * Returns a collection of products containing the attribute and the
     * specified value.
     *
     * @param $items
     * @param string $code
     * @param array $matchedValue
     *
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getByValue($items, string $code, array $matchedValue)
    {
        $products = $this->collectionByItems->get($items);
        return array_filter($products, function (ProductInterface $product) use ($code, $matchedValue) {
            return in_array($product->getData($code), $matchedValue);
        });
    }

    /**
     * Returns a collection of products containing the minimum value for the
     * specified attribute.
     *
     * @param $items
     * @param string $code
     * @param int $minValue
     *
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getByMinValue($items, string $code, int $minValue)
    {
        $products = $this->collectionByItems->get($items);
        return array_filter($products, function (ProductInterface $product) use ($code, $minValue) {
            return ($product->getData($code) > $minValue);
        });
    }
}
