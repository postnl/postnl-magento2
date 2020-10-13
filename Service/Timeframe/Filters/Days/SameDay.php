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

use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Helper\Data;

/**
 * Class SameDay
 * This will filter out days wich are the sameday as the order placementdate.
 * At this moment the extension does not support sameday delivery.
 */
class SameDay implements DaysFilterInterface
{
    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->postNLhelper = $helper;
    }

    /**
     * @param array|object $days
     *
     * @return array|object
     */
    public function filter($days)
    {
        $filteredDays = array_filter($days, function ($value) {
            return $this->postNLhelper->getDate() != $this->postNLhelper->getDate($value->Date);
        });

        return array_values($filteredDays);
    }
}
