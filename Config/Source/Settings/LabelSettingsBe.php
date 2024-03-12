<?php
namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelSettingsBe implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => LabelSettings::LABEL_BOX, 'label' => __('In the box')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
