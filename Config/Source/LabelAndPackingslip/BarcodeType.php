<?php
declare(strict_types=1);

namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Data\OptionSourceInterface;

class BarcodeType implements OptionSourceInterface
{
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
