<?php

namespace TIG\PostNL\Config\Source\Options;

use \Magento\Framework\Option\ArrayInterface;

class FirstLabelPosition implements ArrayInterface
{
    const BOTTOM_RIGHT  = '0';
    const BOTTOM_LEFT = '1';
    const TOP_RIGHT = '2';
    const TOP_LEFT = '3';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        return [
            ['value' => static::BOTTOM_RIGHT, 'label' => __('Bottom Right')],
            ['value' => static::BOTTOM_LEFT, 'label' => __('Bottom Left')],
            ['value' => static::TOP_RIGHT, 'label' => __('Top Right')],
            ['value' => static::TOP_LEFT, 'label' => __('Top Left')],
        ];
        // @codingStandardsIgnoreEnd
    }
}
