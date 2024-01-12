<?php

namespace TIG\PostNL\Config\Source\Options;

use TIG\PostNL\Config\Source\OptionsAbstract;
use Magento\Framework\Data\OptionSourceInterface;

class DeliverydaysOptions extends OptionsAbstract implements OptionSourceInterface
{
    const MAXIMUM_DELIVERY_DAYS = 14;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $output = [];
        for ($number = 1; $number <= static::MAXIMUM_DELIVERY_DAYS; $number++) {
            $output[] = [
                'value' => $number,
                'label' => $number
            ];
        }

        return $output;
    }
}
