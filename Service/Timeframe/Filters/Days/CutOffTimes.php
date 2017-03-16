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

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;

class CutOffTimes implements DaysFilterInterface
{
    /**
     * @var bool
     */
    private $isPastCutOffTime;

    /**
     * @param Webshop           $webshop
     * @param TimezoneInterface $currentDate
     * @param TimezoneInterface $cutOffTime
     */
    public function __construct(
        Webshop $webshop,
        TimezoneInterface $currentDate,
        TimezoneInterface $cutOffTime
    ) {
        $now = $currentDate->date();
        $loadedCutOffTime = $cutOffTime->date('today ' . $webshop->getCutOffTime());

        $diff = $loadedCutOffTime->diff($now);

        $this->isPastCutOffTime = $diff->format('%R') == '+';
    }

    /**
     * @param object|array $days
     *
     * @return array
     */
    public function filter($days)
    {
        if (!$this->isPastCutOffTime) {
            return $days;
        }

        array_shift($days);

        return array_values($days);
    }
}
