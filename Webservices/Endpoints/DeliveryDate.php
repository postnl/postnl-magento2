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

use TIG\PostNL\Webservices\Soap;
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

    /** @var  Soap */
    protected $soap;

    /** @var  Array */
    protected $requestParams;

    /**
     * @param Soap $soap
     */
    public function __construct(Soap $soap)
    {
        $this->soap = $soap;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getDeliveryDate()
    {
        return $this->soap->call($this->type, $this->getWsdlUrl(), $this->requestParams);
    }

    /**
     * @param $address
     *
     * @return array
     */
    public function setRequestData($address)
    {
        $this->requestParams = [
            'GetDeliveryDate' => [
                'City' => 'Amsterdam',
                'CountryCode' => 'NL',
                'Street' => 'Kabelweg',
                'HouseNr' => '37',
                'HouseNrExt' => 'a',
                'PostalCode' => '1014BA',
                'ShippingDate' => '21-12-2016 11:33:13',
                'ShippingDuration' => '1',
                'AllowSundaySorting' => 'true',
                'CutOffTimes' => [
                    [
                        'Day' => '00',
                        'Time' => '14:00:00',
                        'Available' => '1',
                    ],
                    [
                        'Day' => '07',
                        'Time' => '17:00:00',
                        'Available' => '1',
                    ]
                ],
                'Options' => [
                    'Sunday',
                    'Daytime',
                    'Evening',
                ]
            ],
            'Message' => [
                'MessageID' => 'd66d4224eae112b6fa98e59c06043cd8',
                'MessageTimeStamp' => '19-12-2016 07:27:03'
            ]
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
