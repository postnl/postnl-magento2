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

use TIG\PostNL\Service\Timeframe\Options;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Api\CutoffTimes;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;

/**
 * @note : The DeliverDate endpoint is use to get the first possible delivery date, which is needed to collect
 *       the timeframes.
 */
class DeliveryDate extends AbstractEndpoint
{
    /**
     * @var string
     */
    private $version = 'v2_2';

    /**
     * @var string
     */
    private $endpoint = 'calculate/date';

    /**
     * @var string
     */
    private $type = 'GetDeliveryDate';

    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var array
     */
    private $requestParams;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var  CutoffTimes
     */
    private $cutoffTimes;

    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @var Options
     */
    private $timeframeOptions;

    /**
     * DeliveryDate constructor.
     *
     * @param \TIG\PostNL\Webservices\Soap                   $soap
     * @param \TIG\PostNL\Helper\Data                        $postNLhelper
     * @param \TIG\PostNL\Webservices\Api\Message            $message
     * @param \TIG\PostNL\Webservices\Api\CutoffTimes        $cutoffTimes
     * @param \TIG\PostNL\Service\Timeframe\Options          $timeframeOptions
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData
     */
    public function __construct(
        Soap $soap,
        Data $postNLhelper,
        Message $message,
        CutoffTimes $cutoffTimes,
        Options $timeframeOptions,
        ShipmentData $shipmentData
    ) {
        $this->soap = $soap;
        $this->postNLhelper = $postNLhelper;
        $this->message = $message;
        $this->cutoffTimes = $cutoffTimes;
        $this->timeframeOptions = $timeframeOptions;

        parent::__construct(
            $shipmentData
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function call()
    {
        return $this->soap->call($this, $this->type, $this->requestParams);
    }

    /**
     * @param $address
     * @param $shippingDuration
     *
     */
    public function updateParameters($address, $shippingDuration = '1')
    {
        $this->requestParams = [
            'GetDeliveryDate' => [
                'CountryCode'        => $address['country'],
                'PostalCode'         => str_replace(' ', '', $address['postcode']),
                'ShippingDate'       => $this->postNLhelper->getCurrentTimeStamp(),
                'ShippingDuration'   => $shippingDuration,
                'AllowSundaySorting' => $this->timeframeOptions->isSundaySortingAllowed(),
                'CutOffTimes'        => $this->cutoffTimes->get(),
                'Options'            => $this->timeframeOptions->get($address['country']),
            ],
            'Message' => $this->message->get('')
        ];
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version .'/'. $this->endpoint;
    }

    /**
     * @param int $storeId
     */
    public function updateApiKey($storeId)
    {
        $this->soap->updateApiKey($storeId);
    }
}
