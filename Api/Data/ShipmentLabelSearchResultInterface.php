<?php

namespace TIG\PostNL\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ShipmentLabelSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get model list.
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    public function getItems(): array;

    /**
     * Set model list.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentLabelInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
