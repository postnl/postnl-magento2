<?php

namespace TIG\PostNL\Plugin\Order;

class ShipmentPlugin
{
    /**
     * The default getShippingMethod does a explode with the underscore '_' as delimeter.
     *
     * So in our case tig_postnl_reqular was returned as
     * [
     *      carrier_code => 'tig',
     *      method       => 'postnl_regular'
     * ]
     *
     * And that will try to load an 'tig' carrier that doesn't exists, which will trow an exception.
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param string|\Magento\Framework\DataObject $result
     *
     * @return string|\Magento\Framework\DataObject
     */
    // @codingStandardsIgnoreLine
    public function afterGetShippingMethod($subject, $result)
    {
        if (is_string($result) || null === $result) {
            return $result;
        }

        $carrierCode = $result->getData('carrier_code');
        $method      = $result->getData('method');

        if ($carrierCode == 'tig' && $method == 'postnl_regular') {
            $result->setData('carrier_code', 'tig_postnl');
            $result->setData('method', 'regular');
        }

        return $result;
    }
}
