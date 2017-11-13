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

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Address\AbstractAddress as Address;
use TIG\PostNL\Api\Data\OrderInterface as PostNLOrder;
use TIG\PostNL\Service\Timeframe\Options;
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
     * Array
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
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Options
     */
    private $timeframeOptions;

    /**
     * @param Soap              $soap
     * @param TimezoneInterface $timezone
     * @param Options           $timeframeOptions
     * @param Message           $message
     */
    public function __construct(
        Soap $soap,
        TimezoneInterface $timezone,
        Options $timeframeOptions,
        Message $message
    ) {
        $this->soap = $soap;
        $this->message = $message->get('');
        $this->timezone = $timezone;
        $this->timeframeOptions = $timeframeOptions;
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
    public function getLocation()
    {
        return $this->version .'/'. $this->endpoint;
    }

    /**
     * @param Address $address
     * @param $storeId
     * @param PostNLOrder $postNLOrder
     *
     */
    public function setParameters($address, $storeId, PostNLOrder $postNLOrder)
    {
        $this->soap->updateApiKey($storeId);

        $this->requestParams = [
            $this->type => [
                'CountryCode'        => $this->getCountryId(),
                'PostalCode'         => $this->getPostcode($address),
                'HouseNr'            => '',
                'HouseNrExt'         => '',
                'Street'             => '',
                'City'               => $address->getCity(),
                'DeliveryDate'       => $this->getDeliveryDate($address, $postNLOrder),
                'ShippingDuration'   => '1',
                'AllowSundaySorting' => 'true',
                'Options'            => $this->timeframeOptions->get(),
            ],
            'Message' => $this->message
        ];
    }

    /**
     * This endpoint is only available for dutch addresses.
     *
     * @see getPostcode
     * @return string
     */
    private function getCountryId()
    {
        return 'NL';
    }

    /**
     * The sent date webservice can only work with NL addresses. That's why we default to the PostNL Pakketten office
     * postcode for addresses outside the Netherlands.
     *
     * @param Address $address
     *
     * @return string
     */
    private function getPostcode($address)
    {
        if ($address->getCountryId() != 'NL') {
            return '2132WT';
        }

        $postcode = $address->getPostcode();
        $postcode = str_replace(' ', '', $postcode);
        $postcode = strtoupper($postcode);
        $postcode = trim($postcode);

        return $postcode;
    }

    /**
     * @param Address     $address
     * @param PostNLOrder $postNLOrder
     *
     * @return string
     */
    private function getDeliveryDate($address, PostNLOrder $postNLOrder)
    {
        $deliveryDate = $postNLOrder->getDeliveryDate();
        if ($address->getCountryId() == 'NL' || ($address->getCountryId() === null && !empty($deliveryDate))) {
            return $this->formatDate($deliveryDate);
        }

        return $this->formatDate('next weekday');
    }

    /**
     * @param $deliveryDate
     *
     * @return string
     */
    private function formatDate($deliveryDate)
    {
        $date = $this->timezone->date(strtotime($deliveryDate));

        return $date->format('d-m-Y');
    }
}
