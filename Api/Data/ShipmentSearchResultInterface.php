<?php

namespace TIG\PostNL\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ShipmentSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get model list.
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface[]
     */
    public function getItems(): array;

    /**
     * Set model list.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
