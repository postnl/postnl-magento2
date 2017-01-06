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

namespace TIG\PostNL\Validators;

use Symfony\Component\Config\Definition\Exception\Exception;
use TIG\PostNL\Exception as PostnlException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class DeliveryOptions
 *
 * @package TIG\PostNL\Validators
 */
class DeliveryOptions
{
    private $optionParams = [
        'quote_id'                     => [
            'pickup' => true, 'delivery' => true
        ],
        'delivery_date'                => [
            'pickup' => true, 'delivery' => true
        ],
        'expected_delivery_time_start' => [
            'pickup' => true, 'delivery' => true
        ],
        'expected_delivery_time_end'   => [
            'pickup' => false, 'delivery' => true
        ],
        'is_pakjegemak'                => [
            'pickup' => true, 'delivery' => true
        ],
        'pg_location_code'             => [
            'pickup' => true, 'delivery' => false
        ],
        'pg_retail_network_id'         => [
            'pickup' => true, 'delivery' => false
        ]
    ];

    /**
     * @var TimezoneInterface
     */
    private $timeZoneInterface;

    /**
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        TimezoneInterface $timezoneInterface
    ) {
        $this->timeZoneInterface = $timezoneInterface;
    }

    /**
     * @param $params
     */
    public function hasAllRequiredOrderParams($params)
    {
        $requiredOrderParams = $this->requiredOrderParamsMissing($params);
        if (!empty($requiredOrderParams)) {
            throw new Exception(
            // @codingStandardsIgnoreLine
                __('Missing required parameters : %1', var_export($requiredOrderParams))
            );
        }
    }

    /**
     * @param $params
     * @todo needs refactoring
     * @return array
     */
    private function requiredOrderParamsMissing($params)
    {
        $type = 'delivery';
        if (isset($params['is_pakjegemak']) && false != $params['is_pakjegemak']) {
            $type = 'pickup';
        }

        $requiredList = $this->setRequiredListing($type);

        $missing = [];
        foreach ($requiredList as $key => $value) {
            $paramValue = isset($params[$key]) && !empty($params[$key]) ? $params[$key] : false;
            if (!$paramValue && true == $value) {
                $missing[] = $key;
            }
        }

        return $missing;
    }

    /**
     * @param $type
     *
     * @return array
     */
    private function setRequiredListing($type)
    {
        $array = [];

        foreach ($this->optionParams as $key => $value) {
            $array[$key] = $value[$type];
        }

        return $array;
    }

}