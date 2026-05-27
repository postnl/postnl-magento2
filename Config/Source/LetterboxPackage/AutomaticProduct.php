<?php
declare(strict_types=1);

namespace TIG\PostNL\Config\Source\LetterboxPackage;

use Magento\Framework\Data\OptionSourceInterface;
use function __;

class AutomaticProduct implements OptionSourceInterface
{
    public const PRODUCT_LETTERBOX_STANDARD = DefaultProduct::LETTERBOX_PRODUCT_2928;
    public const PRODUCT_LETTERBOX_48 = DefaultProduct::LETTERBOX_PRODUCT_2948;
    public const PRODUCT_CUSTOMER_CHOICE = DefaultProduct::LETTERBOX_PRODUCT_CUSTOMER_CHOICE;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::PRODUCT_LETTERBOX_STANDARD, 'label' => __('Letterboxparcel Standard (24 hours)')],
            ['value' => self::PRODUCT_LETTERBOX_48, 'label' => __('Letterboxparcel 48')],
            ['value' => self::PRODUCT_CUSTOMER_CHOICE, 'label' => __('Let customer decide')],
        ];
    }
}
