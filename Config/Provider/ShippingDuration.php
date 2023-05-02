<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ShippingDuration extends AbstractSource
{
    const CONFIGURATION_VALUE = 'default';
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options[] = [
            'value' => static::CONFIGURATION_VALUE,
            // @codingStandardsIgnoreLine
            'label' => __('Use configuration value')
        ];

        foreach (range(0, 14) as $day) {
            $options[] = [
                'value' => $day,
                'label' => $this->getLabel($day)
            ];
        }

        return $options;
    }

    /**
     * @param $day
     *
     * @return \Magento\Framework\Phrase
     */
    private function getLabel($day)
    {
        if ($day == 1) {
            // @codingStandardsIgnoreLine
            return __('%1 Day', $day);
        }

        // @codingStandardsIgnoreLine
        return __('%1 Days', $day);
    }
}
