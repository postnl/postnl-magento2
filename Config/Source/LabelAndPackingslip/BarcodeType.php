<?php

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Option\ArrayInterface;

class BarcodeType implements ArrayInterface
{
    /**
     * These are the only Zend Barcode types
     * @see \Zend_Barcode_Object_ObjectAbstract
     * which support full length increment ID's.
     *
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => 'code25', 'label' => __('Code 25')],
            ['value' => 'code39', 'label' => __('Code 39')],
            ['value' => 'code128', 'label' => __('Code 128')],
            ['value' => 'royalmail', 'label' => __('Royalmail')],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
