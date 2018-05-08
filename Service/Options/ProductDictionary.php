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
     *
     * @return ProductInterface[]
     */
    public function get($items, array $postNLTypes)
    {
        $products = $this->collectionByItems->get($items);
        return array_filter($products, function (ProductInterface $product) use ($postNLTypes) {
            $attribute = $product->getCustomAttribute(PostNLType::POSTNL_PRODUCT_TYPE);
            $value = $attribute !== null ? $attribute->getValue() : false;
            return in_array($value, $postNLTypes);
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
