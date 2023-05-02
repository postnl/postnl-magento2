<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Management;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\AddressEnhancer;

class GuestPayment
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
     * This is to be Backward compatible with versions below 2.2.6
     * In 2.2.6 the \TIG\PostNL\Plugin\Postcodecheck\Management\Billing Plugin is only called when logged in.
     * This is because in 2.2.6 the assign method is removed within the QuestPaymentInformationManagement.
     * Related to : MAGETWO-89222 - Commit : 4541e3a5fe8deb643bfacdfdc0900c504eba80f5
     *
     * @param                       $subject -> Magento\Checkout\Model\PaymentInformationManagement
     * @param                       $cartId
     * @param                       $email
     * @param PaymentInterface      $paymentMethod
     * @param AddressInterface|null $billingAddress
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function beforeSavePaymentInformation(
        $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if (!$billingAddress) {
            return [$cartId, $email, $paymentMethod, $billingAddress];
        }

        $attributes = $billingAddress->getExtensionAttributes();
        if (empty($attributes) || !$this->webshopConfig->getIsAddressCheckEnabled()) {
            return [$cartId, $email, $paymentMethod, $billingAddress];
        }

        if (!$attributes->getTigHousenumber()) {
            return [$cartId, $email, $paymentMethod, $billingAddress];
        }

        if ($this->isSetBeforeValidation($billingAddress->getStreet(), $attributes->getTigHousenumber())) {
            return [$cartId, $email, $paymentMethod, $billingAddress];
        }

        $billingAddress->setStreet(
            [
                $billingAddress->getStreet()[0],
                $attributes->getTigHousenumber(),
                $attributes->getTigHousenumberAddition()
            ]
        );

        return [$cartId, $email, $paymentMethod, $billingAddress];
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
