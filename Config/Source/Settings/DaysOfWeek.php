<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Option\ArrayInterface;

class DaysOfWeek implements ArrayInterface
{
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        return [
            ['value' => 1, 'label' => __('Monday')],
            ['value' => 2, 'label' => __('Tuesday')],
            ['value' => 3, 'label' => __('Wednesday')],
            ['value' => 4, 'label' => __('Thursday')],
            ['value' => 5, 'label' => __('Friday')],
            ['value' => 6, 'label' => __('Saturday')],
            ['value' => 0, 'label' => __('Sunday')],
        ];
        // @codingStandardsIgnoreEnd
    }
}
