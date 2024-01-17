<?php

namespace TIG\PostNL\Config\Source\General;

use Magento\Framework\Data\OptionSourceInterface;

class Country implements OptionSourceInterface
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
