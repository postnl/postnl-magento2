<?php

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
    public function beforeAssign($subject, $cartId, AddressInterface $address, $shipping = false)
    {
        if (!$address) {
            return [$cartId, $address, $shipping];
        }

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
