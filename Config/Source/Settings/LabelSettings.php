<?php
namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelSettings implements OptionSourceInterface
{
    const LABEL_RETURN = 1;
    const LABEL_BOX = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::LABEL_RETURN, 'label' => __('Shipping & Return label')],
            ['value' => static::LABEL_BOX, 'label' => __('In the box')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
