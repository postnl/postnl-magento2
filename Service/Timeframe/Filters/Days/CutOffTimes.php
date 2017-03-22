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
     * @var \DateTime
     */
    private $now;

    /**
     * @var TimezoneInterface
     */
    private $dateLoader;

    /**
     * @var string
     */
    private $cutOffTime;

    /**
     * @param Webshop           $webshop
     * @param TimezoneInterface $currentDate
     * @param TimezoneInterface $cutOffTime
     * @param TimezoneInterface $dateLoader
     * @param TimezoneInterface $today
     */
    public function __construct(
        Webshop $webshop,
        TimezoneInterface $currentDate,
        TimezoneInterface $cutOffTime,
        TimezoneInterface $dateLoader,
        TimezoneInterface $today
    ) {
        $this->now = $currentDate->date();
        $loadedCutOffTime = $cutOffTime->date('today ' . $webshop->getCutOffTime());
        $diff = $loadedCutOffTime->diff($this->now);

        $this->isPastCutOffTime = $diff->format('%R') == '+';
        $this->dateLoader = $dateLoader;

        $this->today = $today->date('today', null, false);
        $this->cutOffTime = $webshop->getCutOffTime();
    }

    /**
     * @param object|array $days
     *
     * @return array
     */
    public function filter($days)
    {
        if (!$this->isPastCutOffTime || !$this->isNextDay($days[0])) {
            return $days;
        }

        array_shift($days);

        return array_values($days);
    }

    /**
     * @param $day
     *
     * @return bool
     */
    private function isNextDay($day)
    {
        $dayDateTime = $this->dateLoader->date($day->Date, null, false);
        $diff = $dayDateTime->diff($this->today);

        if ($diff->days == 1) {
            return true;
        }

        return false;
    }
}
