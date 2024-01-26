<?php

namespace TIG\PostNL\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface MatrixrateInterfaceSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get model list.
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface[]
     */
    public function getItems(): array;

    /**
     * Set model list.
     *
     * @param \TIG\PostNL\Api\Data\MatrixrateInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
