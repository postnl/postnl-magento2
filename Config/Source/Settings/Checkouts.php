<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Option\ArrayInterface;

class Checkouts implements ArrayInterface
{
    /**
     * Return option array for compatiblity checkout modus.
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => 'default', 'label' => __('Two Step Checkout Luma')],
            ['value' => 'blank', 'label' => __('Two Step Checkout Blank')],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
