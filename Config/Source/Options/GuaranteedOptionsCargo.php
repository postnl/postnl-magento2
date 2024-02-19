<?php

namespace TIG\PostNL\Config\Source\Options;

use Magento\Framework\Option\ArrayInterface;

class GuaranteedOptionsCargo implements ArrayInterface
{
    /**
     * @see \TIG\PostNL\Service\Shipment\GuaranteedOptions
     * @return array
     */
    // @codingStandardsIgnoreStart
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Delivery before 10:00'),
                'value' => '1000'
            ],
            [
                'label' => __('Delivery before 12:00'),
                'value' => '1200'
            ],
            [
                'label' => __('Delivery before 14:00'),
                'value' => '1400'
            ]
        ];
    }
    // @codingStandardsIgnoreEnd
}
