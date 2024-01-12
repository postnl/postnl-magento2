<?php

namespace TIG\PostNL\Plugin\Config\Source;

class AllMethodsPlugin
{
    const POSTNL_CARRIER_CODE     = 'tig_postnl_regular';
    const POSTNL_CARRIER_LABEL    = '[tig_postnl] Regular';

    /**
     * Just like the way getShippingMethod gets his method names, the allMethods from Magento does the same.
     * It explodes the given carrier code with the '_' as delimeter.
     *
     * So in our case the allMethods will return tig_postnl and than extends its name again with that carrier code,
     * which will return tig_postnl_tig_postnl, but it should be tig_postnl_regular.
     *
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $subject
     * @param array $result
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function afterToOptionArray($subject, $result)
    {
        if (!isset($result['tig_postnl'])) {
            return $result;
        }

        // Need to unset the incorrect option
        unset($result['tig_postnl']['value']);

        $result['tig_postnl']['value'][] = [
            'value' => static::POSTNL_CARRIER_CODE,
            'label' => static::POSTNL_CARRIER_LABEL
        ];

        return $result;
    }
}
