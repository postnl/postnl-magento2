<?php

namespace TIG\PostNL\Service\Volume;

use TIG\PostNL\Service\Options\ProductDictionary;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\ProductType as PostNLType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;

abstract class CalculateAbstract
{
    const ATTRIBUTE_VOLUME = 'postnl_parcel_volume';

    // @codingStandardsIgnoreLine
    protected $productDictionary;

    // @codingStandardsIgnoreLine
    protected $shippingOptions;

    /**
     * @param ProductDictionary $productDictionary
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ProductDictionary $productDictionary,
        ShippingOptions $shippingOptions
    ) {
        $this->productDictionary = $productDictionary;
        $this->shippingOptions   = $shippingOptions;
    }

    /**
     * @param $items
     *
     * @return float|int
     */
    // @codingStandardsIgnoreLine
    protected function calculate($items)
    {
        $products = $this->getProducts($items);
        if (empty($products) || !$this->shippingOptions->isExtraAtHomeActive()) {
            return 0;
        }

        $volume = 0;
        foreach ($items as $item) {
            $volume += $this->getVolume($products, $item);
        }

        return round($volume, 0);
    }

    /**
     * @param $products
     * @param ShipmentItemInterface|OrderItemInterface|QuoteItem $item
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function getVolume($products, $item)
    {
        $productId = $this->productDictionary->getProductId($item);
        if (!isset($products[$productId])) {
            return 0;
        }

        /** @var ProductInterface $product */
        $product = $products[$productId];
        return ($product->getData(static::ATTRIBUTE_VOLUME) * $this->getQty($item));
    }

    /**
     * @param ShipmentItemInterface|OrderItemInterface|QuoteItem $item
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    protected function getQty($item)
    {
        return $item->getQty() ?: $item->getQtyOrdered();
    }

    /**
     * @param $items
     *
     * @return ProductInterface[]
     */
    // @codingStandardsIgnoreLine
    protected function getProducts($items)
    {
        /**
         * @codingStandardsIgnoreLine
         * @todo : In future maybe more product types are requiring the volume attribute.
         *         So build within the de backend configuration an multiselect and read it out when using this method.
         */
        return $this->productDictionary->get($items, [PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME]);
    }
}
