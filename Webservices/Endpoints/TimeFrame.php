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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Webservices\Endpoints;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Parser\TimeFrames;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Helper\Data;

/**
 * Class TimeFrame
 *
 * @package TIG\PostNL\Webservices\Calculate
 */
class TimeFrame extends AbstractEndpoint
{
    /**
     * @var string
     */
    private $version = 'v2_0';

    /**
     * @var string
     */
    private $endpoint = 'calculate/timeframes';

    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var Array
     */
    private $requestParams;

    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @var array
     */
    private $message;

    /**
     * @var TimeFrames
     */
    private $timerFramesParser;

    /**
     * @param Soap       $soap
     * @param Message    $message
     * @param TimeFrames $timerFramesParser
     * @param Data       $postNLhelper
     */
    public function __construct(
        Soap $soap,
        Message $message,
        TimeFrames $timerFramesParser,
        Data $postNLhelper
    ) {
        $this->soap = $soap;
        $this->message = $message->get('');
        $this->timerFramesParser = $timerFramesParser;
        $this->postNLhelper = $postNLhelper;
    }

    /**
     * @param bool $parseTimeFrames
     *
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function call($parseTimeFrames = true)
    {
        $response = $this->soap->call($this, 'GetTimeframes', $this->requestParams);

        if ($parseTimeFrames) {
            $timeFrames = $this->getTimeFrames($response);
            return $this->timerFramesParser->handle($timeFrames);
        }

        return $response;
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
     * @codingStandardsIgnoreStart
     * @todo: Add Housenumber validation, can be extracted from $address['street'][1] or regexed out of [0]
     * @todo: Add configuration for sundaysorting (if not enabled Monday should not return)
     * @todo: Remove the @codingStandardsIgnore tags
     * @codingStandardsIgnoreEnd
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
                'HouseNr'            => $address['housenumber'],
                'StartDate'          => $startDate,
                'SundaySorting'      => 'true',
                'EndDate'            => $this->postNLhelper->getEndDate($startDate),
                'Options'            => ['Sunday', 'Daytime', 'Evening']
            ],
            'Message' => $this->message
        ];
    }

    /**
     * @param $response
     *
     * @return mixed
     * @throws LocalizedException
     */
    private function getTimeFrames($response)
    {
        // @codingStandardsIgnoreLine
        $exception = new LocalizedException(__('Invalid GetTimeframes response: %1', var_export($response, true)));
        if (!is_object($response)) {
            throw $exception;
        }

        if (!isset($response->Timeframes)) {
            throw $exception;
        }

        $timeframes = $response->Timeframes;
        if (!isset($timeframes->Timeframe)) {
            throw $exception;
        }

        return $timeframes->Timeframe;
    }
}
