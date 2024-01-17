<?php
namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelDpiSettings implements OptionSourceInterface
{
    const DPI_200 = '200';
    const DPI_300 = '300';
    const DPI_600 = '600';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::DPI_200, 'label' => __('200')],
            ['value' => static::DPI_300, 'label' => __('300')],
            ['value' => static::DPI_600, 'label' => __('600')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
