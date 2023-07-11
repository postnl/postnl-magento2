<?php

namespace TIG\PostNL\Service\Converter;

use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Sales\Model\Order\Address as SalesAddress;

class CanaryIslandToIC
{
    /**
     * @param SalesAddress|QuoteAddress $address
     *
     * @return QuoteAddress|SalesAddress
     */
    public function convert($address)
    {
        if ($this->isCanaryIsland($address)) {
            $address->setCountryId('IC');
        }

        return $address;
    }

    /**
     * POSTNLM2-504 - Canary islands, Melilla and Ceuta should be considered Globalpack
     * https://developer.postnl.nl/browse-apis/send-and-track/products/#destination-EU
     *
     * @param SalesAddress|QuoteAddress $address
     *
     * @return bool
     */
    public function isCanaryIsland($address)
    {
        $canaryIslands = [35, 38, 51, 52];

        // Check if the function is called from SetDefaultData as an object
        if (is_object($address) && $address->getCountryId() === 'ES' &&
            in_array(substr($address->getPostcode(), 0, 2), $canaryIslands)) {
            return true;
        }

        // Check if the address is called from Save as an array
        if (is_array($address) && $address['country'] === 'ES' &&
            in_array(substr($address['postcode'], 0, 2), $canaryIslands)) {
            return true;
        }

        return false;
    }
}
