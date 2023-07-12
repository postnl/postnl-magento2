<?php

namespace TIG\PostNL\Config\Source\Carrier;

use Magento\Framework\Option\ArrayInterface;

class RateType implements ArrayInterface
{
    const CARRIER_RATE_TYPE_FLAT = 'flat';
    const CARRIER_RATE_TYPE_TABLE = 'table';
    const CARRIER_RATE_TYPE_MATRIX = 'matrix';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::CARRIER_RATE_TYPE_FLAT,
                'label' => __('Flat'),
            ],
            [
                'value' => self::CARRIER_RATE_TYPE_TABLE,
                'label' => __('Table'),
            ],
            [
                'value' => self::CARRIER_RATE_TYPE_MATRIX,
                'label' => __('Matrix'),
            ],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
