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

use TIG\PostNL\Helper\Data;
use TIG\PostNL\Service\Timeframe\Filters\OptionsFilterInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;

class Today implements OptionsFilterInterface
{
    const TIMEFRAME_OPTION_TODAY = 'Sameday';

    /** @var Data */
    private $postNLhelper;

    /** @var ShippingOptions */
    private $shippingOptions;

    /**
     * @param Data            $postNLhelper
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        Data $postNLhelper,
        ShippingOptions $shippingOptions
    ) {
        $this->postNLhelper    = $postNLhelper;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param array|object $options
     *
     * @return array|object
     */
    public function filter($timeframes)
    {
        $filterdOptions = array_filter($timeframes, [$this, 'canDeliver']);
        return array_values($filterdOptions);
    }

    /**
     * @param $option
     *
     * @return bool
     */
    public function canDeliver($timeframe)
    {
        $option = $timeframe->Options;
        if (!isset($option->string[0])) {
            return false;
        }

        $todayDate = $this->postNLhelper->getDate();
        $optionDate = $this->postNLhelper->getDate($timeframe->Date);

        $result = false;

        foreach ($option->string as $string) {
            if ($string !== static::TIMEFRAME_OPTION_TODAY && $todayDate != $optionDate) {
                $result = true;
            }

            if ($string === static::TIMEFRAME_OPTION_TODAY
                && $this->shippingOptions->isTodayDeliveryActive()
                && $todayDate == $optionDate) {
                $option->validatedType = $string;
                $result = true;
            }
        }

        return $result;
    }
}
