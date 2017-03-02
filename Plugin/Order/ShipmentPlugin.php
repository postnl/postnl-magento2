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
