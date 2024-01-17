<?php
namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelTypeSettings implements OptionSourceInterface
{
    const TYPE_PDF = 'PDF';
    const TYPE_GIF = 'GIF';
    const TYPE_JPG = 'JPG';
    const TYPE_ZPL = 'ZPL';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::TYPE_PDF, 'label' => __('PDF')],
            ['value' => static::TYPE_GIF, 'label' => __('GIF')],
            ['value' => static::TYPE_JPG, 'label' => __('JPG')],
            ['value' => static::TYPE_ZPL, 'label' => __('ZPL (Zebra)')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
