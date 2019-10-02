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
namespace TIG\PostNL\Plugin\Postcodecheck\Management;

use Magento\Quote\Api\Data\AddressInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\AddressEnhancer;

class Billing
{
    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * @param Webshop $webshopConfig
     */
    public function __construct(
        Webshop $webshopConfig
    ) {
        $this->webshopConfig = $webshopConfig;
    }

    /**
     * @param                  $subject -> Magento\Quote\Model\BillingAddressManagement
     * @param                  $cartId
     * @param AddressInterface $address
     * @param bool             $shipping
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function beforeAssign($subject, $cartId, AddressInterface $address, $shipping = false) {
        $attributes = $address->getExtensionAttributes();
        if (empty($attributes) || !$this->webshopConfig->getIsAddressCheckEnabled()) {
            return [$cartId, $address, $shipping];
        }

        if (!$attributes->getTigHousenumber()) {
            return [$cartId, $address, $shipping];
        }

        if ($this->isSetBeforeValidation($address->getStreet(), $attributes->getTigHousenumber())) {
            return [$cartId, $address, $shipping];
        }

        $address->setStreet(
            [
                $address->getStreet()[0],
                $attributes->getTigHousenumber(),
                $attributes->getTigHousenumberAddition()
            ]
        );

        return [$cartId, $address, $shipping];
    }

    /**
     * @param $street
     * @param $housenumber
     *
     * @return bool
     */
    private function isSetBeforeValidation($street, $housenumber)
    {
        $street  = implode(' ', $street);
        $matched = preg_match(AddressEnhancer::STREET_SPLIT_NAME_FROM_NUMBER, trim($street), $result);
        if (!$matched) {
            return false;
        }

        return $result['number'] == $housenumber;
    }
}
