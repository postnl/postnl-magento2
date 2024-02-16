<?php

namespace TIG\PostNL\Config\Source\Globalpack;

use Magento\Framework\Data\OptionSourceInterface;

class ShipmentType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        return [
            ['value' => 'Gift', 'label' => __('Gift')],
            ['value' => 'Documents', 'label' => __('Documents')],
            ['value' => 'Commercial Goods', 'label' => __('Commercial Goods')],
            ['value' => 'Commercial Sample', 'label' => __('Commercial Sample')],
            ['value' => 'Returned Goods', 'label' => __('Returned Goods')],
        ];
        // @codingStandardsIgnoreEnd
    }
}
