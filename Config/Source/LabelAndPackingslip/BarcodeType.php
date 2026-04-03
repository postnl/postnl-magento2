<?php
declare(strict_types=1);

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Data\OptionSourceInterface;

class BarcodeType implements OptionSourceInterface
{
    /**
     * These are the only Zend Barcode types
     * @see \Zend_Barcode_Object_ObjectAbstract
     * which support full length increment ID's.
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'I25', 'label' => __('Code 25')],
            ['value' => 'C39', 'label' => __('Code 39')],
            ['value' => 'C128', 'label' => __('Code 128')],
            ['value' => 'RMS4CC', 'label' => __('Royalmail')],
        ];
    }
}
