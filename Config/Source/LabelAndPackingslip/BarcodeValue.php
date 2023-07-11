<?php

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Option\ArrayInterface;

class BarcodeValue implements ArrayInterface
{
    const REFEENCE_TYPE_SHIPMENT_ID = 'shipment_increment_id';
    const REFEENCE_TYPE_ORDER_ID    = 'order_increment_id';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => self::REFEENCE_TYPE_SHIPMENT_ID, 'label' => __('Shipment ID')],
            ['value' => self::REFEENCE_TYPE_ORDER_ID, 'label' => __('Order ID')],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
