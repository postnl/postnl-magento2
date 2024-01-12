<?php

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Data\OptionSourceInterface;

class CalculateLabels implements OptionSourceInterface
{
    const CALCULATE_LABELS_WEIGHT = 'weight';
    const CALCULATE_LABELS_PARCEL_COUNT = 'parcel_count';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => self::CALCULATE_LABELS_WEIGHT, 'label' => __("Based on weight")],
            ['value' => self::CALCULATE_LABELS_PARCEL_COUNT, 'label' => __("Based on predefined parcel count")],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
