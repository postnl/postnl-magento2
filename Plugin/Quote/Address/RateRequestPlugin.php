<?php

namespace TIG\PostNL\Plugin\Quote\Address;

class RateRequestPlugin
{
    /**
     * When a customer is logged-in and try's to checkout the error is thrown saying 'Please select a shippingmethod'
     * This is because of the limitCarrier that is set with 'tig' as value. Later on in the process of the checkout the
     * carrier config is requested. Magento will request this like carrier/[carrier_code].
     *
     * @param $subject
     * @param $key
     * @param $value
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function beforeSetData($subject, $key, $value)
    {
        if ($key == 'limit_carrier' && $value == 'tig') {
            return [$key, 'tig_postnl'];
        }

        return [$key, $value];
    }
}
