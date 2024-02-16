<?php

namespace TIG\PostNL\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ShipmentBarcodeSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get model list.
     *
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface[]
     */
    public function getItems(): array;

    /**
     * Set model list.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentBarcodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
