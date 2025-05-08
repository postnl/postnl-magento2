<?php

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
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
     * @var Webshop
     */
    private $webshopProvider;

    /**
     * @var TimezoneInterface
     */
    private $currentDate;

    /**
     * @var IsPastCutOff
     */
    private $isPastCutOff;

    /**
     * @param Data              $helper
     * @param Webshop           $webshopProvider
     * @param TimezoneInterface $currentDate
     * @param IsPastCutOff      $isPastCutOff
     */
    public function __construct(
        Data $helper,
        Webshop $webshopProvider,
        TimezoneInterface $currentDate,
        IsPastCutOff $isPastCutOff
    ) {
        $this->postNLhelper    = $helper;
        $this->webshopProvider = $webshopProvider;
        $this->currentDate     = $currentDate;
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
                $shipmentDays = explode(',', $this->webshopProvider->getShipmentDays());

                $daysToCheck = 1;
                while ($daysToCheck < 8) {
                    $nextDate = strtotime($checkDay . ' +' . $daysToCheck . ' day');
                    $dateToCheck = $this->currentDate->date($nextDate)->format('d-m-Y');

                    $weeknumber = $this->postNLhelper->getDayOrWeekNumber($dateToCheck);
                     if (in_array($weeknumber, $shipmentDays)) {
                         $checkDay = $nextDate;
                         break;
                     }

                    $daysToCheck++;
                }
            }

            $todayDate     = $this->currentDate->date('today', null, true, false);
            $timeframeDate = $this->postNLhelper->getDate($value->Date);

            return $todayDate->format('Y-m-d') != $timeframeDate;
        });

        return array_values($filteredDays);
    }
}
