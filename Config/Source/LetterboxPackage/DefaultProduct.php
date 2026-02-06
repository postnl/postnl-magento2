<?php

namespace TIG\PostNL\Config\Source\LetterboxPackage;

use TIG\PostNL\Config\Source\OptionsAbstract;
use Magento\Framework\Data\OptionSourceInterface;

class DefaultProduct extends OptionsAbstract implements OptionSourceInterface
{

    const LETTERBOX_PRODUCT_2928 = '2928';
    const LETTERBOX_PRODUCT_2948 = '2948';
    const LETTERBOX_PRODUCT_CUSTOMER_CHOICE = 'customer_choice';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::LETTERBOX_PRODUCT_2928,
                'label' => __('Letterboxparcel Standard (24 hours)'),
            ],
            [
                'value' => self::LETTERBOX_PRODUCT_2948,
                'label' => __('Letterboxparcel 48'),
            ],
            [
                'value' => self::LETTERBOX_PRODUCT_CUSTOMER_CHOICE,
                'label' => __('Let customer decide'),
            ],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
