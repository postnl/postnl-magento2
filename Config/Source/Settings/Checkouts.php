<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class Checkouts implements OptionSourceInterface
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
