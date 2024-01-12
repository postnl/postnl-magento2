<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class CutOffSettings implements OptionSourceInterface
{
    /**
     * Returns options for 0:00 till 23:45
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        for ($hour = 0; $hour < 24; $hour++) {
            // @codingStandardsIgnoreLine
            $options = array_merge($options, $this->addHour($hour));
        }

        return $options;
    }

    /**
     * Returns options for 0:00 till 10:00
     *
     * @return array
     */
    public function getTodayOptions()
    {
        $options = [];

        for ($hour = 0; $hour < 10; $hour++) {
            // @codingStandardsIgnoreLine
            $options = array_merge($options, $this->addHour($hour));
        }

        // addHour() adds till x:45, but we want to end at an x:00, so add 10:00 separately.
        // @codingStandardsIgnoreLine
        $options[] = ['value' => '10:00:00', 'label' => __('10:00')];

        return $options;
    }

    /**
     * @param $hour
     *
     * @return array
     */
    private function addHour($hour)
    {
        $hour    = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $options = [];

        // @codingStandardsIgnoreStart
        $options[] = ['value' => $hour . ':00:00', 'label' => __($hour . ':00')];
        $options[] = ['value' => $hour . ':15:00', 'label' => __($hour . ':15')];
        $options[] = ['value' => $hour . ':30:00', 'label' => __($hour . ':30')];
        $options[] = ['value' => $hour . ':45:00', 'label' => __($hour . ':45')];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
