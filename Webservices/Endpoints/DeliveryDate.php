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

use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Api\CutoffTimes;

/**
 * @note : The DeliverDate endpoint is use to get the first possible delivery date, which is needed to collect
 *       the timeframes.
 */
class DeliveryDate extends AbstractEndpoint
{
    /**
     * @var string
     */
    private $version = 'v2_1';

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
     * @var Array
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
     * @param Soap            $soap
     * @param Data            $postNLhelper
     * @param Message         $message
     * @param CutoffTimes     $cutoffTimes
     */
    public function __construct(
        Soap $soap,
        Data $postNLhelper,
        Message $message,
        CutoffTimes $cutoffTimes
    ) {
        $this->soap = $soap;
        $this->postNLhelper = $postNLhelper;
        $this->message = $message;
        $this->cutoffTimes = $cutoffTimes;
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
     * @codingStandardsIgnoreStart
     * @todo: 1. Calculation for shippingDuration
     * @todo: 2. Add configuration for sundaysorting (if not enabled Monday should not return)
     * @todo: 3. Move surounding @codingStandardsIgnore tags
     * @codingStandardsIgnoreEnd
     * @param $address
     *
     * @return array
     */
    public function setParameters($address)
    {
        $this->requestParams = [
            'GetDeliveryDate' => [
                'CountryCode'        => $address['country'],
                'PostalCode'         => str_replace(' ', '', $address['postcode']),
                'ShippingDate'       => $this->postNLhelper->getCurrentTimeStamp(),
                'ShippingDuration'   => '1',
                'AllowSundaySorting' => 'true',
                'CutOffTimes'        => $this->cutoffTimes->get(),
                'Options'            => $this->postNLhelper->getDeliveryTimeframesOptions()
            ],
            'Message' => $this->message->get('')
        ];
    }

    /**
     * @return string
     */
    public function getWsdlUrl()
    {
        return 'DeliveryDateWebService/2_1/';
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version .'/'. $this->endpoint;
    }
}
