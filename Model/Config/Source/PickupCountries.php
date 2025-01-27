<?php

namespace TIG\PostNL\Model\Config\Source;

class PickupCountries implements \Magento\Framework\Data\OptionSourceInterface
{
    public const COUNTRY_DE = 'DE';
    public const COUNTRY_FR = 'FR';
    public const COUNTRY_DK = 'DK';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::COUNTRY_DE, 'label' => __('Germany')],
            ['value' => self::COUNTRY_FR, 'label' => __('France')],
            ['value' => self::COUNTRY_DK, 'label' => __('Denmark')]
        ];
    }

    public function toArray(): array
    {
        return [
            self::COUNTRY_DE => __('Germany'),
            self::COUNTRY_FR => __('France'),
            self::COUNTRY_DK => __('Denmark')
        ];
    }
}
