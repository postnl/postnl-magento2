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

/**
 * @todo : Waiting on PostNL to finish the API for DeliveryDate, so needs to be refactored when API is ready.
 */
use TIG\PostNL\Webservices\SoapOld;
use TIG\PostNL\Webservices\Helpers\Deliveryoptions;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Api\CutoffTimes;

/**
 * Class DeliveryDate
 * @note : The DeliverDate endpoint is use to get the first posable delivery date, which is needed to collect
 *       the timeframes.
 *
 * @package TIG\PostNL\Webservices\Calculate
 */
class DeliveryDate
{
    /** @var string */
    protected $version = '2_1';

    /** @var string */
    protected $service = 'DeliveryDateWebService';

    /** @var string */
    protected $type = 'GetDeliveryDate';

    /** @var  SoapOld */
    protected $soap;

    /** @var  Array */
    protected $requestParams;

    /** @var Deliveryoptions */
    protected $deliveryOptionsHelper;

    /** @var Message */
    protected $message;

    /** @var  CutoffTimes */
    protected $cutoffTimes;

    /** @var Data  */
    protected $postNLhelper;

    /**
     * @param SoapOld         $soap
     * @param Data            $postNLhelper
     * @param Deliveryoptions $deliveryoptions
     * @param Message         $message
     * @param CutoffTimes     $cutoffTimes
     */
    public function __construct(
        SoapOld $soap,
        Data $postNLhelper,
        Deliveryoptions $deliveryoptions,
        Message $message,
        CutoffTimes $cutoffTimes
    ) {
        $this->soap = $soap;
        $this->deliveryOptionsHelper = $deliveryoptions;
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
        return $this->soap->call($this->type, $this->getWsdlUrl(), $this->requestParams);
    }

    /**
     * @todo :  1. Calculation for shippingDuration
     * @todo:   2. Add configuration for sundaysorting (if not enabled Monday should not return)
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
                'Options'            => $this->deliveryOptionsHelper->getDeliveryDatesOptions()
            ],
            'Message' => $this->message->get('')
        ];
    }

    /**
     * @return string
     */
    protected function getWsdlUrl()
    {
        return $this->service .'/'. $this->version;
    }
}
