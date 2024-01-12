<?php

namespace TIG\PostNL\Config\Source\Matrixrate;

use Magento\Framework\Data\OptionSourceInterface;

class ParcelType implements OptionSourceInterface
{
    private const PARCEL_TYPE_PAKJEGEMAK        = 'pakjegemak';
    private const PARCEL_TYPE_EXTRA_AT_HOME     = 'extra@home';
    private const PARCEL_TYPE_REGULAR           = 'regular';
    private const PARCEL_TYPE_LETTERBOX_PACKAGE = 'letterbox_package';

    /**
     * Option array for select option
     *
     * @param $isMultiselect
     * @return array[]
     */
    public function toOptionArray($isMultiselect = false)
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::PARCEL_TYPE_PAKJEGEMAK,
                'label' => __('Post office'),
            ],
            [
                'value' => self::PARCEL_TYPE_EXTRA_AT_HOME,
                'label' => __('Extra@Home'),
            ],
            [
                'value' => self::PARCEL_TYPE_REGULAR,
                'label' => __('Regular'),
            ],
            [
                'value' => self::PARCEL_TYPE_LETTERBOX_PACKAGE,
                'label' => __('(International) Letterbox Package'),
            ],
        ];
        // @codingStandardsIgnoreEnd
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please select--')]);
            array_unshift($options, ['value' => '*', 'label' => __('All parcel types')]);
        }

        return $options;
    }
}
