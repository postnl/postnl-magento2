<?php

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Option\ArrayInterface;

class ShowShippingLabel implements ArrayInterface
{
    const SHOW_SHIPPING_LABEL_TOGETHER = 'together';
    const SHOW_SHIPPING_LABEL_SEPARATE = 'separate';
    const SHOW_SHIPPING_LABEL_NONE     = 'none';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => self::SHOW_SHIPPING_LABEL_TOGETHER, 'label' => __("Print the shipping label and packing slip on the same page")],
            ['value' => self::SHOW_SHIPPING_LABEL_SEPARATE, 'label' => __("Print the shipping label on a separate page")],
            ['value' => self::SHOW_SHIPPING_LABEL_NONE, 'label' => __("Don't print the shipping label")]
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
