<?php

namespace TIG\PostNL\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get model list.
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface[]
     */
    public function getItems(): array;

    /**
     * Set model list.
     *
     * @param \TIG\PostNL\Api\Data\OrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
