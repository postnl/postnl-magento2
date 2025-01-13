<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ShippingDataProvider
{
    private array $collections = [];

    private CollectionFactory $productCollectionFactory;

    public function __construct(
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function loadCollection(array $productIds): Collection
    {
        $key = implode('_', $productIds);
        if (!isset($this->collections[$key])) {
            $collection = $this->productCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            $collection->addAttributeToSelect([
                'weight',
                'postnl_max_qty_letterbox',
                'postnl_max_qty_international',
                'postnl_max_qty_international_letterbox',
            ]);
            $this->collections[$key] = $collection;
        }
        return $this->collections[$key];
    }
}
