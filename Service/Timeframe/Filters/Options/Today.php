<?php

namespace TIG\PostNL\Service\Timeframe\Filters\Options;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Service\Timeframe\Filters\OptionsFilterInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;

class Today implements OptionsFilterInterface
{
    const TIMEFRAME_OPTION_TODAY = 'Today';

    /** @var Data */
    private $postNLhelper;

    /**
     * @var Webshop
     */
    private $webshopProvider;

    /** @var ShippingOptions */
    private $shippingOptions;
    /**
     * @var TimezoneInterface
     */
    private $currentDate;
    /**
     * @var IsPastCutOff
     */
    private $isPastCutOff;

    /**
     * @param Data              $postNLhelper
     * @param Webshop           $webshopProvider
     * @param TimezoneInterface $currentDate
     * @param ShippingOptions   $shippingOptions
     * @param IsPastCutOff      $isPastCutOff
     */
    public function __construct(
        Data $postNLhelper,
        Webshop $webshopProvider,
        TimezoneInterface $currentDate,
        ShippingOptions $shippingOptions,
        IsPastCutOff $isPastCutOff
    ) {
        $this->postNLhelper    = $postNLhelper;
        $this->webshopProvider = $webshopProvider;
        $this->shippingOptions = $shippingOptions;
        $this->currentDate     = $currentDate;
        $this->isPastCutOff    = $isPastCutOff;
    }

    /**
     * @param array|object $timeframes
     *
     * @return array|object
     */
    public function filter($timeframes)
    {
        $filterdOptions = array_filter($timeframes, [$this, 'canDeliver']);
        return array_values($filterdOptions);
    }

    /**
     * @param $timeframe
     *
     * @return bool
     */
    public function canDeliver($timeframe)
    {
        $option = $timeframe->Options;
        if (!isset($option->string[0])) {
            return false;
        }

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

        $todayDate  = $this->currentDate->date('today', null, true, false)->format('Y-m-d');
        $cutoffDate = $this->currentDate->date($checkDay, null, true, false)->format('Y-m-d');
        $optionDate = $this->postNLhelper->getDate($timeframe->Date);
        $result     = false;

        foreach ($option->string as $string) {
            if ($string !== static::TIMEFRAME_OPTION_TODAY && $todayDate != $optionDate && $cutoffDate != $optionDate) {
                $result = true;
            }

            if ($string === static::TIMEFRAME_OPTION_TODAY
                && $this->shippingOptions->isTodayDeliveryActive()
                && $cutoffDate == $optionDate) {
                $option->validatedType = $string;
                $result = true;
            }
        }

        return $result;
    }
}
