<?php

namespace TIG\PostNL\Config\Source\LetterboxPackage;

use TIG\PostNL\Config\Source\OptionsAbstract;
use Magento\Framework\Option\ArrayInterface;

class CalculationMode extends OptionsAbstract implements ArrayInterface
{

    const CALCULATION_MODE_AUTOMATIC = 'automatic';
    const CALCULATION_MODE_MANUALLY = 'manually';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::CALCULATION_MODE_AUTOMATIC,
                'label' => __('Automatic'),
            ],
            [
                'value' => self::CALCULATION_MODE_MANUALLY,
                'label' => __('Manually'),
            ],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
