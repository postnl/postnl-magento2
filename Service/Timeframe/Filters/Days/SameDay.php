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
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;

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
     * @var TimezoneInterface
     */
    private $currentDate;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var IsPastCutOff
     */
    private $isPastCutOff;

    /**
     * @param Data              $helper
     * @param TimezoneInterface $currentDate
     * @param ShippingOptions   $shippingOptions
     * @param IsPastCutOff      $isPastCutOff
     */
    public function __construct(
        Data $helper,
        TimezoneInterface $currentDate,
        ShippingOptions $shippingOptions,
        IsPastCutOff $isPastCutOff
    ) {
        $this->postNLhelper    = $helper;
        $this->currentDate     = $currentDate;
        $this->shippingOptions = $shippingOptions;
        $this->isPastCutOff    = $isPastCutOff;
    }

    /**
     * @param array|object $days
     *
     * @return array|object
     */
    public function filter($days)
    {
        $filteredDays = array_filter($days, function ($value) {
            $checkDay = 'today';
            if ($this->isPastCutOff->calculate()) {
                $checkDay = 'tomorrow';
            }

            $todayDate     = $this->currentDate->date('today', null, true, false);
            $cutoffDate    = $this->currentDate->date($checkDay, null, true, false)->format('Y-m-d');
            $timeframeDate = $this->postNLhelper->getDate($value->Date);
            $weekday       = $this->postNLhelper->getDayOrWeekNumber($todayDate->format('H:i:s'));

            if ($this->shippingOptions->isTodayDeliveryActive()
                && $cutoffDate == $timeframeDate
                && !in_array($weekday, ['6', '7'])
                && !$this->isPastCutOff->calculateToday()
            ) {
                return true;
            }

            return $todayDate->format('Y-m-d') != $timeframeDate;
        });

        return array_values($filteredDays);
    }
}
