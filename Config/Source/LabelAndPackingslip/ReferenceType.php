<?php

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Option\ArrayInterface;

class ReferenceType implements ArrayInterface
{
    const REFEENCE_TYPE_NONE        = 'none';
    const REFEENCE_TYPE_SHIPMENT_ID = 'shipment_increment_id';
    const REFEENCE_TYPE_ORDER_ID    = 'order_increment_id';
    const REFEENCE_TYPE_CUSTOM      = 'custom';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => self::REFEENCE_TYPE_NONE, 'label' => __('None')],
            ['value' => self::REFEENCE_TYPE_SHIPMENT_ID, 'label' => __('Shipment ID')],
            ['value' => self::REFEENCE_TYPE_ORDER_ID, 'label' => __('Order ID')],
            ['value' => self::REFEENCE_TYPE_CUSTOM, 'label' => __('Use a custom value')]
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
