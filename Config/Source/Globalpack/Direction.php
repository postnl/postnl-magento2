<?php

namespace TIG\PostNL\Config\Source\Globalpack;

use Magento\Framework\Data\OptionSourceInterface;

class Direction implements OptionSourceInterface
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
