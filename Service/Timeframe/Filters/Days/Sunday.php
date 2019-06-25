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
namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;

class Sunday implements DaysFilterInterface
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    public function __construct(
        Data $helper,
        ShippingOptions $shippingOptions
    ) {
        $this->helper = $helper;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param object|array $days
     *
     * @return array
     */
    public function filter($days)
    {
        if ($this->shippingOptions->isSundayDeliveryActive()) {
            return $days;
        }

        $filtered = array_filter($days, function ($day) {
            $date = $day->Date;
            $dayOfWeek = $this->helper->getDayOrWeekNumber($date, 'w');

            return $dayOfWeek !== 7;
        });

        return array_values($filtered);
    }
}
