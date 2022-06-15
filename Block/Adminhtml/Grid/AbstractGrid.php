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
