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
namespace TIG\PostNL\Service\Parcel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use TIG\PostNL\Config\Provider\ProductType as PostNLType;
use TIG\PostNL\Service\Options\ProductDictionary;
use TIG\PostNL\Config\Provider\ShippingOptions;

abstract class CountAbstract
{
    const ATTRIBUTE_PARCEL_COUNT = 'postnl_parcel_count';
    const WEIGHT_PER_PARCEL      = 20000;

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
     * @param int $weight
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     *
     * @return int|\Magento\Framework\Api\AttributeInterface|null
     */
    // @codingStandardsIgnoreLine
    protected function calculate($weight, $items)
    {
        $products = $this->getProducts($items);
        if (empty($products) || !$this->shippingOptions->isExtraAtHomeActive()) {
            $remainingParcelCount = ceil($weight / self::WEIGHT_PER_PARCEL);
            return $remainingParcelCount < 1 ? 1 : $remainingParcelCount;
        }

        /**
         * If there are more items the count start with one for the items without parcel_count
         * If there is one item it should start with zero parcels because only the parcel_count should be added.
         */
        $parcelCount = $this->getStartCount($items);
        foreach ($items as $item) {
            $parcelCount += $this->getParcelCount($products, $item);
        }

        return $parcelCount < 1 ? 1 : $parcelCount;
    }

    /**
     * @param $products
     * @param ShipmentItemInterface|OrderItemInterface|QuoteItem $item
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    protected function getParcelCount($products, $item)
    {
        if (!isset($products[$item->getProductId()])) {
            return 0;
        }

        /** @var ProductInterface $product */
        $product = $products[$item->getProductId()];
        $productParcelCount = $product->getCustomAttribute(self::ATTRIBUTE_PARCEL_COUNT);
        return ($productParcelCount->getValue() * $this->getQty($item));
    }

    /**
     * Parcel count is only needed if a specific product type is found within the items.
     *
     * @param $items
     *
     * @return ProductInterface[]
     */
    // @codingStandardsIgnoreLine
    protected function getProducts($items)
    {
        /**
         * @codingStandardsIgnoreLine
         * @todo : In future maybe more product types are requiring the parcel_count attribute.
         *         So build within the de backend configuration an multiselect and read it out when using this method.
         */
        return $this->productDictionary->get($items, [PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME]);
    }

    /**
     * @param $items
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function getWeight($items)
    {
        $weight = 0;
        /** @var QuoteItem $item */
        foreach ($items as $item) {
            /** @noinspection PhpUndefinedMethodInspection */
            $weight += $item->getWeight();
        }

        return $weight;
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
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     *
     * @return int
     */
    //@codingStandardsIgnoreLine
    protected function getStartCount($items)
    {
        /** In most situations */
        $startCount = count($items) == 1 ? 0 : 1;

        /** But for a configurable the parent is also added to items so the start should be 0 */
        foreach ($items as $item) {
            $startCount = $this->getStartCountBasedOnType($item, $startCount);
        }

        return $startCount;
    }

    /**
     * @param ShipmentItemInterface|OrderItemInterface|QuoteItem $item
     * @param int $startCount
     *
     * @return int;
     */
    //@codingStandardsIgnoreLine
    protected function getStartCountBasedOnType($item, $startCount)
    {
        if ($item->getProductType() !== 'simple') {
            $startCount = 0;
        }

        return $startCount;
    }
}
