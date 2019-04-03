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

use Magento\Customer\Model\Address\AbstractAddress as Address;
use TIG\PostNL\Api\Data\OrderInterface as PostNLOrder;
use TIG\PostNL\Service\Timeframe\Options;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;
use TIG\PostNL\Webservices\Api\CutoffTimes;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;
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
     * @var CutoffTimes
     */
    private $cutoffTimes;
    
    /**
     * @var Options
     */
    private $timeframeOptions;
    
    /**
     * @var DeliveryDateFallback
     */
    private $dateFallback;
    
    /**
     * SentDate constructor.
     *
     * @param \TIG\PostNL\Webservices\Soap                     $soap
     * @param \TIG\PostNL\Webservices\Api\CutoffTimes          $cutoffTimes
     * @param \TIG\PostNL\Service\Timeframe\Options            $timeframeOptions
     * @param \TIG\PostNL\Webservices\Api\Message              $message
     * @param \TIG\PostNL\Webservices\Api\DeliveryDateFallback $dateFallback
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments   $shipmentData
     */
    public function __construct(
        Soap $soap,
        CutoffTimes $cutoffTimes,
        Options $timeframeOptions,
        Message $message,
        DeliveryDateFallback $dateFallback,
        ShipmentData $shipmentData
    ) {
        $this->soap             = $soap;
        $this->message          = $message->get('');
        $this->cutoffTimes      = $cutoffTimes;
        $this->timeframeOptions = $timeframeOptions;
        $this->dateFallback     = $dateFallback;
        
        parent::__construct(
            $shipmentData
        );
    }
    
    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
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
        return $this->version . '/' . $this->endpoint;
    }
    
    /**
     * @param                                     $address
     * @param                                     $storeId
     * @param \TIG\PostNL\Api\Data\OrderInterface $postNLOrder
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
                'ShippingDuration'   => '1', // Request by PostNL not to use $postNLOrder->getShippingDuration()
                'AllowSundaySorting' => $this->timeframeOptions->isSundaySortingAllowed(),
                'Options'            => $this->timeframeOptions->get($this->getCountryId())
            ],
            'Message'   => $this->message
        ];
    }
    
    /**
     * This endpoint is only available for dutch addresses.
     *
     * @return string
     * @see getPostcode
     */
    private function getCountryId()
    {
        return 'NL';
    }
    
    /**
     * The sent date webservice can only work with NL addresses. That's why we default use the PostNL Pakketten office
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
        if ($deliveryDate == null) {
            return $this->dateFallback->get();
        }
        
        if (in_array($address->getCountryId(), ['NL', 'BE'])
            || ($address->getCountryId() === null && !empty($deliveryDate))) {
            return $this->dateFallback->getDate($deliveryDate);
        }
        
        return $this->dateFallback->get();
    }
}
