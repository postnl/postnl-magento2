<?php
declare(strict_types=1);

namespace TIG\PostNL\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Minicart implements OptionSourceInterface
{
    public const MINICART_BEFORE_BUTTONS = 'before_buttons';
    public const MINICART_AFTER_BUTTONS = 'after_buttons';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::MINICART_BEFORE_BUTTONS, 'label' => __('Before Minicart Buttons')],
            ['value' => self::MINICART_AFTER_BUTTONS, 'label' => __('After Minicart Buttons')],
        ];
    }
}
