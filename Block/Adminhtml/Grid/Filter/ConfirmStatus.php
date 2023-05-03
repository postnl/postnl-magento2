<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Filter;

use Magento\Framework\Data\OptionSourceInterface;

class ConfirmStatus implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            //@codingStandardsIgnoreStart
            ['label' => __('Confirmed'), 'value' => 1],
            ['label' => __('Not confirmed'), 'value' => 0]
            //@codingStandardsIgnoreEnd
        ];
    }
}
