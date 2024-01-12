<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelsizeSettings implements OptionSourceInterface
{
    const A4_LABELSIZE = 'A4';
    const A6_LABELSIZE = 'A6';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::A4_LABELSIZE, 'label' => __('A4 Format')],
            ['value' => static::A6_LABELSIZE, 'label' => __('A6 Format')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
