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

use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Soap;

class SentDate extends AbstractEndpoint
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
    private $type = 'GetSentDate';

    /**
     * @var Array
     */
    private $requestParams;

    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var array
     */
    private $message;
    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @param Soap    $soap
     * @param Data    $postNLhelper
     * @param Message $message
     */
    public function __construct(
        Soap $soap,
        Data $postNLhelper,
        Message $message
    ) {
        $this->soap = $soap;
        $this->message = $message->get('');
        $this->postNLhelper = $postNLhelper;
    }

    /**
     * @throws \Magento\Framework\Webapi\Exception
     * @return mixed
     */
    public function call()
    {
        $response = $this->soap->call($this, $this->type, $this->requestParams);

        return $response->SentDate;
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

    /**
     * @param Shipment    $shipment
     *
     * @param PostNLOrder $postNLOrder
     *
     * @return array
     */
    public function setParameters(Shipment $shipment, PostNLOrder $postNLOrder)
    {
        $address = $shipment->getShippingAddress();

        $this->requestParams = [
            $this->type => [
                'CountryCode'        => $address->getCountryId(),
                'PostalCode'         => str_replace(' ', '', $address->getPostcode()),
                'HouseNr'            => '',
                'HouseNrExt'         => '',
                'Street'             => '',
                'City'               => $address->getCity(),
                'DeliveryDate'       => $this->postNLhelper->getDateDmy($postNLOrder->getDeliveryDate()),
                'ShippingDuration'   => '1',
                'AllowSundaySorting' => 'true',
                'Options'            => $this->postNLhelper->getDeliveryTimeframesOptions()
            ],
            'Message' => $this->message
        ];
    }
}
