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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
namespace TIG\PostNL\Service\Converter;

use Magento\Sales\Model\Order\Address as SalesAddress;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

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
        if ($address->getCountryId() === 'ES' && in_array(substr($address->getPostcode(), 0, 2), $canaryIslands)) {
            return true;
        }

        return false;
    }
}
