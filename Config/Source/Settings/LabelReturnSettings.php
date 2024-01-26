<?php
namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelReturnSettings implements OptionSourceInterface
{
    const LABEL_RETURN_ALL = 1;
    const LABEL_RETURN_ORDER = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::LABEL_RETURN_ALL, 'label' => __('Activate return function for all labels')],
            ['value' => static::LABEL_RETURN_ORDER, 'label' => __('Activate return function on an order-by-order basis')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
