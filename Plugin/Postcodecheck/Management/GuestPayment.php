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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
