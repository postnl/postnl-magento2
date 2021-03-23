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
            $attribute = $product->getData($code);
            $value = $attribute !== null ? $attribute : false;
            return in_array($value, $matchedValue);
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
            $attribute = $product->getData($code);
            $value = $attribute !== null ? $attribute : false;
            return ($value > $minValue);
        });
    }
}
