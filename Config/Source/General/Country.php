<?php

namespace TIG\PostNL\Config\Source\General;

use Magento\Framework\Option\ArrayInterface;

class Country implements ArrayInterface
{
    const COUNTRY_NL = 'NL';
    const COUNTRY_BE = 'BE';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::COUNTRY_NL,
                'label' => __('Netherlands'),
            ],
            [
                'value' => self::COUNTRY_BE,
                'label' => __('Belgium'),
            ]
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
