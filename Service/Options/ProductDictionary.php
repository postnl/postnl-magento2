<?php

namespace TIG\PostNL\Service\Options;

use TIG\PostNL\Config\Provider\ProductType as PostNLType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderItemInterface;

use TIG\PostNL\Service\Product\CollectionByItems;

class ProductDictionary
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
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     * @param array $postNLTypes
     * @param bool $convertLetterbox - When handling Customs data for GP in Service/Shipment/Customs/SortItems.php
     *                                 we aren't expecting letterbox parcels, these should be considered regular
     *
     * @return ProductInterface[]
     */
    public function get($items, array $postNLTypes, $convertLetterbox = false)
    {
        $products = $this->collectionByItems->get($items);

        return array_filter($products, function (ProductInterface $product) use ($postNLTypes, $convertLetterbox) {
            if ($convertLetterbox &&
                $product->getData(PostNLType::POSTNL_PRODUCT_TYPE) == PostNLType::PRODUCT_TYPE_LETTERBOX_PACKAGE
            ) {
                $product->setData(PostNLType::POSTNL_PRODUCT_TYPE, PostNLType::PRODUCT_TYPE_REGULAR);
            }

            return in_array($product->getData(PostNLType::POSTNL_PRODUCT_TYPE), $postNLTypes);
        });
    }

    /**
     * When the item is part of a bundle or a configurable item->getProductId will return the parentId.
     * Thats why we will load the product by sku to be sure to retrieve the correct product ID,
     * otherwise the volume and weight could be wrong.
     *
     * @param ShipmentItemInterface|OrderItemInterface|QuoteItem $item
     * @return int
     */
    public function getProductId($item)
    {
        $product = $this->productRepository->get($item->getSku());
        if (!$product->getId()) {
            return $item->getProductId();
        }

        return $product->getId();
    }
}
