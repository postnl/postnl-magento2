<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class ReturnTypes implements OptionSourceInterface
{
    const TYPE_FREE_POST = 1; //or Reply number
    const TYPE_HOME_ADDRESS = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::TYPE_FREE_POST, 'label' => __('Freepost number')],
            ['value' => static::TYPE_HOME_ADDRESS, 'label' => __('Home address')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
