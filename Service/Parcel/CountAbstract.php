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
use TIG\PostNL\Model\Product\Attribute\Source\Type as PostNLType;

abstract class CountAbstract
{
    const ATTRIBUTE_PARCEL_COUNT = 'postnl_parcel_count';
    const WEIGHT_PER_PARCEL      = 20000;

    // @codingStandardsIgnoreLine
    protected $productDictionary;

    /**
     * @param ProductDictionary $productDictionary
     */
    public function __construct(ProductDictionary $productDictionary)
    {
        $this->productDictionary = $productDictionary;
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
        $products    = $this->getProducts($items);
        if (empty($products)) {
            $remainingParcelCount = ceil($weight / self::WEIGHT_PER_PARCEL);
            return $remainingParcelCount < 1 ? 1 : $remainingParcelCount;
        }

        $parcelCount = 0;
        foreach ($items as $item) {
            $product = $products[$item->getProductId()];
            $productParcelCount = $product->getCustomAttribute(self::ATTRIBUTE_PARCEL_COUNT);
            $parcelCount += ($productParcelCount->getValue() * $this->getQty($item));
        }

        return $parcelCount < 1 ? 1 : $parcelCount;
    }

    /**
     * @param $items
     *
     * @return ProductInterface[]
     */
    // @codingStandardsIgnoreLine
    protected function getProducts($items)
    {
        return $this->productDictionary->get($items, PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME);
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
        return $item->getQty() !== null ? $item->getQty() : $item->getQtyOrdered();
    }
}
