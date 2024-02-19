<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Management;

use Magento\Quote\Api\Data\AddressInterface;
use TIG\PostNL\Config\Provider\Webshop;

class Shipping
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
     * @param                   $subject -> Magento\Quote\Model\ShippingAddressManagement
     * @param                   $cartId
     * @param AddressInterface  $address
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function beforeAssign($subject, $cartId, AddressInterface $address)
    {
        if (!$address) {
            return [$cartId, $address];
        }

        $attributes = $address->getExtensionAttributes();
        if (empty($attributes) || !$this->webshopConfig->getIsAddressCheckEnabled()) {
            return [$cartId, $address];
        }

        if (!$attributes->getTigHousenumber()) {
            return [$cartId, $address];
        }

        $address->setStreet(
            [
                $address->getStreet()[0],
                $attributes->getTigHousenumber(),
                $attributes->getTigHousenumberAddition()
            ]
        );

        return [$cartId, $address];
    }
}
