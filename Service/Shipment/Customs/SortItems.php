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
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class SortItems
{
    const MAX_CUSTOMS_CONTENT_ROWS = 5;

    /**
     * @var Globalpack
     */
    private $globalpackConfig;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $storeId;

    /**
     * @var CollectionFactory
     */
    private $productCollection;

    private $attributeToSort;

    private $attributeSortDirection;

    /**
     * SortItems constructor.
     *
     * @param Globalpack        $globalpack
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Globalpack $globalpack,
        CollectionFactory $collectionFactory
    ) {
        $this->globalpackConfig  = $globalpack;
        $this->productCollection = $collectionFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment|ShipmentInterface $shipment
     *
     * @return array
     */
    public function get($shipment)
    {
        $this->storeId = $shipment->getStoreId();
        $this->attributeToSort = $this->globalpackConfig->getProductSortingAttributeCode($this->storeId);
        $this->attributeSortDirection = $this->globalpackConfig->getProductSortingDirection($this->storeId);

        $items = $this->filterItems($shipment->getItems());
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
            $sortItems[$item->getId()] = $attributeValues[$item->getProductId()];
        }

        natsort($sortItems);
        if ($this->attributeSortDirection == 'desc') {
            $sortItems = array_reverse($sortItems, true);
        }

        foreach ($items as $item) {
            $sortItems[$item->getId()] = $item;
        }

        return $sortItems;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Item[]|ShipmentItemInterface[] $items
     *
     * @return \Magento\Sales\Model\Order\Shipment\Item[]|ShipmentItemInterface[]
     */
    private function filterItems($items)
    {
        return array_filter($items, function ($item) {
            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            $productType = $item->getOrderItem()->getProductType();
            return !($item->isDeleted() || $productType == 'bundle');
        });
    }

    /**
     * @param $items
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function getProductCollection($items)
    {
        $productCollection = $this->productCollection->create();
        $productCollection->setStore($this->storeId);
        $productCollection->addAttributeToSelect($this->attributeToSort);
        $productCollection->addFieldToFilter('entity_id', ['in' => $this->getAllProductIds($items)]);
        $productCollection->setOrder($this->attributeToSort, strtoupper($this->attributeSortDirection));
        $productCollection->setPageSize(static::MAX_CUSTOMS_CONTENT_ROWS);

        return $productCollection;
    }

    /**
     * @param $items
     *
     * @return array
     */
    private function getAllProductIds($items)
    {
        $ids = [];
        /** @var ShipmentItemInterface $item */
        foreach ($items as $item) {
            $ids[] = $item->getProductId();
        }

        return $ids;
    }
}
