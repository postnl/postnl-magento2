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
namespace TIG\PostNL\Plugin\Postcodecheck\Mageplaza;

use Magento\Checkout\Api\Data\ShippingInformationInterface;

class CheckoutManagement
{
    // @codingStandardsIgnoreLine
    public function beforeSaveCheckoutInformation(
        $subject,
        $cartId,
        ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    ) {
        if (empty($customerAttributes)) {
            return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
        }

        if (!isset($customerAttributes['tig_housenumber'])) {
            return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
        }

        $shippingAddress = $addressInformation->getShippingAddress();

        $shippingAddress->setStreet(
            $this->parseHousenumber($shippingAddress->getStreet(), $customerAttributes)
        );

        $addressInformation->setShippingAddress($shippingAddress);

        return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
    }

    /**
     * @param $customerAttributes
     *
     * @return string
     */
    private function parseStreet($street, $customerAttributes)
    {
//        if (is_array($street)) {
//            $street = $street[0];
//        }
        if (isset($customerAttributes['tig_housenumber_addition'])) {
            return $street . ' ' .
                $customerAttributes['tig_housenumber'] . ' ' .
                $customerAttributes['tig_housenumber_addition'];
        }
        return $street . ' ' . $customerAttributes['tig_housenumber'];
    }
}
