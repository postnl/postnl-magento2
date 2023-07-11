<?php

namespace TIG\PostNL\Config\Source\Options;

use TIG\PostNL\Config\Source\OptionsAbstract;
use Magento\Framework\Option\ArrayInterface;

class StockOptions extends OptionsAbstract implements ArrayInterface
{
    /**
     * Return option array
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => 'backordered', 'label' => __('Always show deliveryoptions')],
            ['value' => 'in_stock', 'label' => __('Only for products that are in stock')]
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
