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
