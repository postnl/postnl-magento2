<?php

namespace TIG\PostNL\Config\Source\Options;

use Magento\Framework\Data\OptionSourceInterface;

class GuaranteedOptionsPackages implements OptionSourceInterface
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
                'label' => __('Delivery before 17:00'),
                'value' => '1700'
            ]
        ];
    }
    // @codingStandardsIgnoreEnd
}
