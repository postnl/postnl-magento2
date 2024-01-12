<?php

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
            $this->parseStreet($shippingAddress->getStreet(), $customerAttributes)
        );

        $addressInformation->setShippingAddress($shippingAddress);

        return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
    }

    /**
     * @param $street
     * @param $customerAttributes
     *
     * @return array
     */
    private function parseStreet($street, $customerAttributes)
    {
        if (!is_array($street)) {
            $street = [$street];
        }

        $street[1] = $customerAttributes['tig_housenumber'];
        if (isset($customerAttributes['tig_housenumber_addition'])) {
            $street[2] = $customerAttributes['tig_housenumber_addition'];
        }

        return $street;
    }
}
