<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class InsuredTiers implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => 'default', 'label' => __('Total order value')],
            ['value' => '100',     'label' => __('1 - 100')],
            ['value' => '250',     'label' => __('101 - 250')],
            ['value' => '500',     'label' => __('251 - 500')],
            ['value' => '1000',    'label' => __('501 - 1000')],
            ['value' => '1500',    'label' => __('1001 - 1500')],
            ['value' => '2000',    'label' => __('1501 - 2000')],
            ['value' => '2500',    'label' => __('2001 - 2500')],
            ['value' => '3000',    'label' => __('2501 - 3000')],
            ['value' => '3500',    'label' => __('3001 - 3500')],
            ['value' => '4000',    'label' => __('3501 - 4000')],
            ['value' => '4500',    'label' => __('4001 - 4500')],
            ['value' => '5000',    'label' => __('4501 - 5000')],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
