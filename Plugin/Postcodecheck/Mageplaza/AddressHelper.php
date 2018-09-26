<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
