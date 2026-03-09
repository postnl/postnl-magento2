<?php
declare(strict_types=1);

namespace TIG\PostNL\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Cart implements OptionSourceInterface
{
    public const CART_BEFORE_CHECKOUT = 'cart_before';
    public const CART_AFTER_CHECKOUT = 'cart_after';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::CART_BEFORE_CHECKOUT, 'label' => __('Before Checkout Button')],
            ['value' => self::CART_AFTER_CHECKOUT, 'label' => __('After Checkout Button')]
        ];
    }
}
