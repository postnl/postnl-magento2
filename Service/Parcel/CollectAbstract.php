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

use TIG\PostNL\Config\Provider\ProductType as PostNLType;
use TIG\PostNL\Service\Options\ProductDictionary;
use TIG\PostNL\Service\Product\CollectionByAttributeValue;

abstract class CollectAbstract
{
    const ATTRIBUTE_PARCEL_COUNT = 'postnl_parcel_count';

    /** @var \TIG\PostNL\Service\Options\ProductDictionary $productDictionary */
    // @codingStandardsIgnoreLine
    protected $productDictionary;

    /** @var \TIG\PostNL\Service\Product\CollectionByAttributeValue $collectionByAttributeValue */
    // @codingStandardsIgnoreLine
    protected $collectionByAttributeValue;

    /**
     * CollectAbstract constructor.
     *
     * @param \TIG\PostNL\Service\Options\ProductDictionary $productDictionary
     * @param \TIG\PostNL\Service\Product\CollectionByAttributeValue $collectionByAttributeValue
     */
    public function __construct(
        ProductDictionary $productDictionary,
        CollectionByAttributeValue $collectionByAttributeValue
    ) {
        $this->productDictionary = $productDictionary;
        $this->collectionByAttributeValue = $collectionByAttributeValue;
    }

    /**
     * @param $items
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    // @codingStandardsIgnoreLine
    protected function getProductsByType($items)
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
    // @codingStandardsIgnoreLine
    protected function getProductsWithoutParcelCount($items)
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
    // @codingStandardsIgnoreLine
    protected function getProductsWithParcelCount($items)
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
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    // @codingStandardsIgnoreLine
    protected function getExtraAtHomeProducts($items)
    {
        return $this->collectionByAttributeValue->getByValue(
            $items,
            PostNLType::POSTNL_PRODUCT_TYPE,
            [PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME]
        );
    }
}
