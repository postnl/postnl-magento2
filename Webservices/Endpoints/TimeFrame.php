<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Webservices\Helpers\Deliveryoptions;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Helper\Data;

/**
 * Class TimeFrame
 *
 * @package TIG\PostNL\Webservices\Calculate
 */
class TimeFrame extends AbstractEndpoint
{
    const TIMEFRAME_OPTION_EVENING = 'Evening';
    const TIMEFRAME_OPTION_DAYTIME = 'Daytime';

    /** @var string  */
    protected $version = 'v2_0';

    /** @var string  */
    protected $endpoint = 'calculate/timeframes';

    /** @var  Soap */
    protected $soap;

    /** @var  Array */
    protected $requestParams;

    /** @var Deliveryoptions */
    protected $deliveryOptionsHelper;

    /** @var ShippingOptions */
    protected $shippingOptions;

    /** @var Data  */
    protected $postNLhelper;

    /** @var Message */
    protected $message;

    /**
     * @param Soap            $soap
     * @param Deliveryoptions $deliveryoptions
     * @param Data            $postNLhelper
     * @param ShippingOptions $shippingOptions
     * @param Message         $message
     */
    public function __construct(
        Soap $soap,
        Deliveryoptions $deliveryoptions,
        Data $postNLhelper,
        ShippingOptions $shippingOptions,
        Message $message
    ) {
        $this->soap = $soap;
        $this->deliveryOptionsHelper = $deliveryoptions;
        $this->shippingOptions = $shippingOptions;
        $this->postNLhelper  = $postNLhelper;
        $this->message = $message;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function call()
    {
        return $this->soap->call($this, 'GetTimeframes', $this->requestParams);
    }

    /**
     * @return string
     */
    public function getWsdlUrl()
    {
        return 'TimeframeWebService/2_0/';
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }

    /**
     * @todo : Add Housenumber validation, can be extracted from $address['street'][1] or regexed out of [0]
     * @todo:  Add configuration for sundaysorting (if not enabled Monday should not return)
     * @param $address
     * @param $startDate
     *
     * @return array
     */
    public function setParameters($address, $startDate)
    {
        $this->requestParams = [
            'Timeframe' => [
                'CountryCode'        => $address['country'],
                'PostalCode'         => str_replace(' ', '', $address['postcode']),
                'HouseNr'            => '37',
                'StartDate'          => $startDate,
                'SundaySorting'      => 'true',
                'EndDate'            => $this->deliveryOptionsHelper->getEndDate($startDate),
                'Options'            => $this->deliveryOptionsHelper->getDeliveryDatesOptions()
            ],
            'Message' => $this->message->get('')
        ];
    }

    /**
     * @todo : Filter on Monday and Sunday delivery also on evening and CutoffTimes.
     * @todo : Optimize code in calisthenics way.
     *
     * @param $timeFrames
     * @param $country
     *
     * @return array
     */
    public function filterTimeFrames($timeFrames, $country)
    {
        $filterdTimeFrames = [];
        foreach ($timeFrames as $timeFrame) {
            if ($this->isSameDay($timeFrame->Date) && !$this->canUseSameDay()) {
                continue;
            }
            $filterdTimeFrames = $this->getTimeFrameOptions(
                $filterdTimeFrames,
                $timeFrame->Timeframes->TimeframeTimeFrame,
                $timeFrame->Date
            );
        }

        return $filterdTimeFrames;
    }

    /**
     * @todo : Optimize code in calisthenics way.
     * @param $filterdTimeFrames
     * @param $timeFrames
     * @param $date
     *
     * @return array
     */
    protected function getTimeFrameOptions(&$filterdTimeFrames, $timeFrames, $date)
    {
        foreach ($timeFrames as $timeFrame) {
            if (!$this->validateOnEvening($timeFrame->Options->string[0])) {
                continue;
            }

            $filterdTimeFrames[] = [
                'day'    => date('D', strtotime($date)) . ' (' . $date . ')',
                'from'   => $timeFrame->From,
                'to'     => $timeFrame->To,
                'option' => $timeFrame->Options->string[0],
                'date'   => $date
            ];
        }

        return $filterdTimeFrames;
    }

    /**
     * @todo : Move to validation Classes.
     *
     * @param $option
     *
     * @return bool
     */
    protected function validateOnEvening($option)
    {
        if ($option !== self::TIMEFRAME_OPTION_EVENING) {
            return true;
        }

        if ($option === self::TIMEFRAME_OPTION_EVENING && $this->shippingOptions->isEveningDeliveryActive()) {
            return true;
        }

        return false;
    }

    /**
     * @todo : Add logic to check if the request is passed to CuttOff times.
     * @todo : Move to validation Classes.
     *
     * @return bool
     */
    protected function canUseSameDay()
    {
        if (!$this->shippingOptions->isSameDayDeliveryActive()) {
            return false;
        }

        return true;
    }

    /**
     * @todo : Move to validation Classes.
     * @param $timeFrameDate
     *
     * @return bool
     */
    protected function isSameDay($timeFrameDate)
    {
        if ($this->postNLhelper->getDateYmd() == $this->postNLhelper->getDateYmd($timeFrameDate)) {
            return true;
        }
        return false;
    }
}
