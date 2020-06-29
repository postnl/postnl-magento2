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
namespace TIG\PostNL\Service\Shipment\Customs;

use TIG\PostNL\Config\Provider\Globalpack;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\ProductType;
use TIG\PostNL\Service\Options\ProductDictionary;

class SortItems
{
    const MAX_CUSTOMS_CONTENT_ROWS = 5;

    /**
     * @var Globalpack
     */
    private $globalpackConfig;

    /**
     * @var ProductDictionary
     */
    private $productDictionary;

    /**
     * @var ProductType
     */
    private $productType;

    /**
     * @var string
     */
    private $attributeToSort;

    /**
     * @var string
     */
    private $attributeSortDirection;

    /**
     * SortItems constructor.
     *
     * @param Globalpack        $globalpack
     * @param ProductDictionary $productDictionary
     * @param ProductType       $productType
     */
    public function __construct(
        Globalpack $globalpack,
        ProductDictionary $productDictionary,
        ProductType $productType
    ) {
        $this->globalpackConfig  = $globalpack;
        $this->productDictionary = $productDictionary;
        $this->productType       = $productType;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment|ShipmentInterface $shipment
     *
     * @return array
     */
    public function get($shipment)
    {
        $this->attributeToSort = $this->globalpackConfig->getProductSortingAttributeCode($shipment->getStoreId());
        $this->attributeSortDirection = $this->globalpackConfig->getProductSortingDirection($shipment->getStoreId());

        $items = $shipment->getItems();
        /** @noinspection PhpUndefinedMethodInspection */
        $items = $this->filterItems(is_array($items) ? $items : $items->getItems());
        return $this->sort($items);
    }

    /**
     * @param $items
     *
     * @return array
     */
    private function sort($items)
    {
        $attributeValues = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($this->getProductCollection($items) as $product) {
            $attributeValues[$product->getId()] = $product->getDataUsingMethod($this->attributeToSort);
        }

        $sortItems = [];
        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($items as $item) {
            $sortItems[$item->getId()] = $attributeValues[$this->productDictionary->getProductId($item)];
        }

        natsort($sortItems);
        foreach ($items as $item) {
            $sortItems[$item->getId()] = $item;
        }

        return $this->attributeSortDirection == 'desc' ? array_reverse($sortItems, true) : $sortItems;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Item[]|ShipmentItemInterface[] $items
     *
     * @return \Magento\Sales\Model\Order\Shipment\Item[]|ShipmentItemInterface[]
     */
    private function filterItems($items)
    {
        $filtered = [];
        foreach ($items as $item) {
            $filtered = $this->setItemsArray($item, $filtered);
        }

        $items = array_filter($filtered, function ($item) {
            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            return !$item->isDeleted();
        });

        return $items;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Item $item
     * @param $filtered
     *
     * @return array $filtered
     */
    private function setItemsArray($item, $filtered)
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $item->getOrderItem();

        $filtered[$item->getProductId()] = $item;
        if ($orderItem->getProductType() == 'bundle') {
            unset($filtered[$orderItem->getProductId()]);
            $filtered = array_merge($filtered, $orderItem->getChildrenItems());
        }

        return array_values($filtered);
    }

    /**
     * @param $items
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    private function getProductCollection($items)
    {
        $filters = array_merge($this->productType->getAllTypes($items), [false]);
        return $this->productDictionary->get($items, $filters);
    }
}
