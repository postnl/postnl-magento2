<?php

namespace TIG\PostNL\Config\Source\Globalpack;

use Magento\Framework\Option\ArrayInterface;

class Direction implements ArrayInterface
{
    /**
     * @return array
     */

    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        return [
            [
                'value' => 'asc',
                'label' => __('Ascending')
            ],
            [
                'value' => 'desc',
                'label' => __('Descending')
            ]
        ];
        // @codingStandardsIgnoreEnd
    }
}
