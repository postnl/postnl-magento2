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
namespace TIG\PostNL\Webservices\Endpoints;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Service\Timeframe\Options;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Parser\TimeFrames;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Helper\Data;

class TimeFrame extends AbstractEndpoint
{
    /**
     * @var string
     */
    private $version = 'v2_1';

    /**
     * @var string
     */
    private $endpoint = 'calculate/timeframes';

    /**
     * @var Soap
     */
    private $soap;

    /**
     * Array
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
     * @var Options
     */
    private $timeframeOptions;

    /**
     * @param Soap       $soap
     * @param Message    $message
     * @param TimeFrames $timerFramesParser
     * @param Data       $postNLhelper
     * @param Options    $timeframeOptions
     */
    public function __construct(
        Soap $soap,
        Message $message,
        TimeFrames $timerFramesParser,
        Data $postNLhelper,
        Options $timeframeOptions
    ) {
        $this->soap = $soap;
        $this->message = $message->get('');
        $this->timerFramesParser = $timerFramesParser;
        $this->postNLhelper = $postNLhelper;
        $this->timeframeOptions = $timeframeOptions;
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
                'Options'            => $this->timeframeOptions->get(),
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

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->soap->updateApiKey($storeId);
    }
}
