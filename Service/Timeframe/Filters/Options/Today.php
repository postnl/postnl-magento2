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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Timeframe\Filters\Options;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Service\Timeframe\Filters\OptionsFilterInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;

class Today implements OptionsFilterInterface
{
    const TIMEFRAME_OPTION_TODAY = 'Today';

    /** @var Data */
    private $postNLhelper;

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
     * @param TimezoneInterface $currentDate
     * @param ShippingOptions   $shippingOptions
     * @param IsPastCutOff      $isPastCutOff
     */
    public function __construct(
        Data $postNLhelper,
        TimezoneInterface $currentDate,
        ShippingOptions $shippingOptions,
        IsPastCutOff $isPastCutOff
    ) {
        $this->postNLhelper    = $postNLhelper;
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
            $checkDay = 'tomorrow';
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
