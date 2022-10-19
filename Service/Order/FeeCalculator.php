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
namespace TIG\PostNL\Service\Order;

use TIG\PostNL\Config\Provider\ShippingOptions;

class FeeCalculator
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param $params
     *
     * @return float
     */
    public function get($params)
    {
        if (!array_key_exists('option', $params)) {
            return (float)0.0;
        }

        if ($this->shippingOptions->isEveningDeliveryActive() && $params['option'] == 'Evening') {
            return (float)$this->getEveningDeliveryFee($params);
        }

        if ($this->shippingOptions->isSundayDeliveryActive() && $params['option'] == 'Sunday') {
            return (float)$this->shippingOptions->getSundayDeliveryFee();
        }

        if ($this->shippingOptions->isTodayDeliveryActive() && $params['option'] == 'Today') {
            return (float)$this->shippingOptions->getTodayDeliveryFee();
        }

        return (float)0.0;
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    private function getEveningDeliveryFee($params)
    {
        $country = isset($params['country']) ? $params['country'] : 'NL';
        return $this->shippingOptions->getEveningDeliveryFee($country);
    }

    /**
     * @param array $params
     *
     * @return float
     */
    public function statedAddressOnlyFee($params)
    {
        if ($this->shippingOptions->isStatedAddressOnlyActive() && $params['stated_address_only']) {
            return (float)$this->shippingOptions->getStatedAddressOnlyFee();
        }

        return (float)0.0;
    }
}
