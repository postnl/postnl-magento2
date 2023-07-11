<?php

namespace TIG\PostNL\Block\Adminhtml\Grid;

use Magento\Ui\Component\Listing\Columns\Column;

abstract class AbstractGrid extends Column
{
    /**
     * @var array
     */
    // @codingStandardsIgnoreLine
    protected $items = [];

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $this->items = $dataSource['data']['items'];

            $this->prepareData();
            $this->handleItems();

            $dataSource['data']['items'] = $this->items;
        }

        return $dataSource;
    }

    /**
     * Load all the needed data in only 1 query.
     */
    // @codingStandardsIgnoreLine
    protected function prepareData()
    {
        return null;
    }


    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function handleItems()
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index][$this->getData('name')] = $this->getCellContents($item);
        }
    }

    /**
     * @param object $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    abstract protected function getCellContents($item);
}
