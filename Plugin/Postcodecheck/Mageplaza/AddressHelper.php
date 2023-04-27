<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Mageplaza;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\Webshop;

class AddressHelper
{
    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * AddressHelper constructor.
     *
     * @param AccountConfiguration $accountConfiguration
     * @param Webshop              $webshop
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        Webshop $webshop
    ) {
        $this->accountConfiguration = $accountConfiguration;
        $this->webshopConfig = $webshop;
    }

    /**
     * Subject => \Mageplaza\Osc\Helper\Address
     * Compatible plugin, Mageplaza skippes grouped fields, so we need to add this manualy.
     *
     * @param         $subject
     * @param         $fieldPosition
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function afterGetAddressFieldPosition($subject, $fieldPosition)
    {
        if ($this->accountConfiguration->isModusOff() || !$this->webshopConfig->getIsAddressCheckEnabled()) {
            return $fieldPosition;
        }

        return $this->addPostcodeFieldGroup($fieldPosition);
    }

    /**
     * @param $fieldPosition
     *
     * @return mixed
     */
    private function addPostcodeFieldGroup($fieldPosition)
    {
        $fieldPosition = $this->orderFields($fieldPosition);

        $fieldPosition['postcode-field-group'] = [
            'sortOrder' => 65,
            'colspan'   => 12,
            'isNewRow'  => true
        ];

        return $fieldPosition;
    }

    /**
     * @see \Mageplaza\Osc\Block\Checkout\LayoutProcessor::addAddressOption
     * Because the postcode-field-group is not an Mageplaza OSC field it will be ignored.
     * Affects since OSC version 2.5.0
     *
     * @param $fieldPosition
     * @return mixed
     */
    private function orderFields(&$fieldPosition)
    {
        $sortOrder = 40;
        foreach ($fieldPosition as $key => &$field) {
            $field['sortOrder'] = $sortOrder += 10;
        }

        return $fieldPosition;
    }
}
