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
namespace TIG\PostNL\Webservices\Parser;

use TIG\PostNL\Helper\Data;
use TIG\PostNL\Config\Provider\ShippingOptions;
use Magento\Framework\Locale\ListsInterface;

class TimeFrames
{
    const TIMEFRAME_OPTION_EVENING = 'Evening';
    const TIMEFRAME_OPTION_DAYTIME = 'Daytime';

    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var ListsInterface
     */
    private $locale;

    /**
     * @param Data            $postNLhelper
     * @param ShippingOptions $shippingOptions
     * @param ListsInterface  $locale
     */
    public function __construct(
        Data $postNLhelper,
        ShippingOptions $shippingOptions,
        ListsInterface $locale
    ) {
        $this->postNLhelper = $postNLhelper;
        $this->shippingOptions = $shippingOptions;
        $this->locale = $locale;
    }

    /**
     * @codingStandardsIgnoreLine
     * @todo : Filter on Monday and Sunday delivery also on evening and CutoffTimes.
     * @param $timeFrames
     *
     * @return array
     */
    public function handle($timeFrames)
    {
        $filterdTimeFrames = array_filter($timeFrames, function ($value) {
            return !$this->isSameDay($value->Date);
        });

        return array_map(function ($timeFrame) {
            $frames = $timeFrame->Timeframes;
            return $this->getTimeFrameOptions(
                $filterdTimeFrames,
                $frames->TimeframeTimeFrame,
                $timeFrame->Date
            );
        }, $filterdTimeFrames);
    }

    /**
     * @param $filterdTimeFrames
     * @param $timeFrames
     * @param $date
     *
     * @return array
     */
    private function getTimeFrameOptions(&$filterdTimeFrames, $timeFrames, $date)
    {
        $timeFrames = array_filter($timeFrames, function ($value) {
            $options = $value->Options;
            return $this->validateOnEvening($options->string[0]);
        });

        foreach ($timeFrames as $timeFrame) {
            $options = $timeFrame->Options;
            $filterdTimeFrames[] = [
                'day'           => $this->getDayOfWeek($date),
                'from'          => $timeFrame->From,
                'from_friendly' => substr($timeFrame->From, 0, 5),
                'to'            => $timeFrame->To,
                'to_friendly'   => substr($timeFrame->To, 0, 5),
                'option'        => $options->string[0],
                'date'          => $date,
            ];
        }

        return $filterdTimeFrames;
    }

    /**
     * @codingStandardsIgnoreLine
     * @todo : Move to validation Classes.
     *
     * @param $option
     *
     * @return bool
     */
    private function validateOnEvening($option)
    {
        if ($option !== static::TIMEFRAME_OPTION_EVENING) {
            return true;
        }

        if ($option === static::TIMEFRAME_OPTION_EVENING && $this->shippingOptions->isEveningDeliveryActive()) {
            return true;
        }

        return false;
    }

    /**
     * @codingStandardsIgnoreLine
     * @todo : Move to validation Classes.
     * @param $timeFrameDate
     *
     * @return bool
     */
    private function isSameDay($timeFrameDate)
    {
        if ($this->postNLhelper->getDateYmd() == $this->postNLhelper->getDateYmd($timeFrameDate)) {
            return true;
        }

        return false;
    }

    /**
     * @param $date
     *
     * @return bool|string
     */
    private function getDayOfWeek($date)
    {
        $weekdays = $this->locale->getOptionWeekdays();

        return $weekdays[date('w', strtotime($date))]['label'];
    }
}
