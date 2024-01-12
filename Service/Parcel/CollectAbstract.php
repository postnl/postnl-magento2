<?php

namespace TIG\PostNL\Service\Parcel;

use TIG\PostNL\Config\Provider\ProductType as PostNLType;
use TIG\PostNL\Service\Options\ProductDictionary;
use TIG\PostNL\Service\Product\CollectionByAttributeValue;
use TIG\PostNL\Config\Provider\ShippingOptions;

abstract class CollectAbstract
{
    const ATTRIBUTE_PARCEL_COUNT = 'postnl_parcel_count';

    /** @var \TIG\PostNL\Service\Options\ProductDictionary $productDictionary */
    private $productDictionary;

    /** @var \TIG\PostNL\Service\Product\CollectionByAttributeValue $collectionByAttributeValue */
    private $collectionByAttributeValue;

    /** @var \TIG\PostNL\Config\Provider\ShippingOptions $shippingOptions */
    private $shippingOptions;
    
    /**
     * CollectAbstract constructor.
     *
     * @param \TIG\PostNL\Service\Options\ProductDictionary          $productDictionary
     * @param \TIG\PostNL\Service\Product\CollectionByAttributeValue $collectionByAttributeValue
     * @param \TIG\PostNL\Config\Provider\ShippingOptions            $shippingOptions
     */
    public function __construct(
        ProductDictionary $productDictionary,
        CollectionByAttributeValue $collectionByAttributeValue,
        ShippingOptions $shippingOptions
    ) {
        $this->productDictionary = $productDictionary;
        $this->collectionByAttributeValue = $collectionByAttributeValue;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param $items
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductsByType($items)
    {
        return $this->productDictionary->get(
            $items,
            [PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME, PostNLType::PRODUCT_TYPE_REGULAR]
        );
    }

    /**
     * @param $items
     *
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductsWithoutParcelCount($items)
    {
        return $this->collectionByAttributeValue->getByValue(
            $items,
            self::ATTRIBUTE_PARCEL_COUNT,
            [null, 0, false]
        );
    }

    /**
     * @param $items
     *
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductsWithParcelCount($items)
    {
        return $this->collectionByAttributeValue->getByMinValue(
            $items,
            self::ATTRIBUTE_PARCEL_COUNT,
            0
        );
    }

    /**
     * @param $items
     *
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]|null
     */
    public function getExtraAtHomeProducts($items)
    {
        if (!$this->shippingOptions->isExtraAtHomeActive()) {
            return null;
        }

        return $this->collectionByAttributeValue->getByValue(
            $items,
            PostNLType::POSTNL_PRODUCT_TYPE,
            [PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME]
        );
    }
}
