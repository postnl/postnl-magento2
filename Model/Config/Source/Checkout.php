<?php
declare(strict_types=1);

namespace TIG\PostNL\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Checkout implements OptionSourceInterface
{
    public const CHECKOUT_BEFORE_EMAIL = 'before_email';
    public const CHECKOUT_BEFORE_DETAILS = 'before_detail';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::CHECKOUT_BEFORE_EMAIL, 'label' => __('Before Customer Email')],
            ['value' => self::CHECKOUT_BEFORE_DETAILS, 'label' => __('Before Customer Details')]
        ];
    }
}
