<?php

namespace TIG\PostNL\Config\Source\General;

use Magento\Framework\Data\OptionSourceInterface;

class Modus implements OptionSourceInterface
{
    /**
     * Return modus option array
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => '1', 'label' => __('Live')],
            ['value' => '2', 'label' => __('Test')],
            ['value' => '0', 'label' => __('Off')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
