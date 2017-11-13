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
namespace TIG\PostNL\Helper\DeliveryOptions;

use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Service\Order\FeeCalculator;
use TIG\PostNL\Service\Order\ProductCodeAndType;

class OrderParams
{
    private $optionParams = [
        'quote_id'                     => [
            'pickup' => true,
            'delivery' => true
        ],
        'delivery_date'                => [
            'pickup' => true,
            'delivery' => true
        ],
        'expected_delivery_time_start' => [
            'pickup' => false,
            'delivery' => true
        ],
        'expected_delivery_time_end'   => [
            'pickup' => false,
            'delivery' => true
        ],
        'is_pakjegemak'                => [
            'pickup' => true,
            'delivery' => false
        ],
        'pg_location_code'             => [
            'pickup' => true,
            'delivery' => false
        ],
        'pg_retail_network_id'         => [
            'pickup' => true,
            'delivery' => false
        ],
        'pg_address'                   => [
            'pickup' => true,
            'delivery' => false
        ]
    ];
    /**
     * @var FeeCalculator
     */
    private $feeCalculator;

    /**
     * @var ProductCodeAndType
     */
    private $productCodeAndType;

    /**
     * @param FeeCalculator      $feeCalculator
     * @param ProductCodeAndType $productCodeAndType
     */
    public function __construct(
        FeeCalculator $feeCalculator,
        ProductCodeAndType $productCodeAndType
    ) {
        $this->feeCalculator      = $feeCalculator;
        $this->productCodeAndType = $productCodeAndType;
    }

    /**
     * @param $params
     *
     * @return array
     * @throws PostnlException
     */
    public function get($params)
    {
        $type                = $params['type'];
        $params              = $this->formatParamData($params);
        $requiredOrderParams = $this->requiredOrderParamsMissing($type, $params);

        if (!empty($requiredOrderParams)) {
            throw new PostnlException(
            // @codingStandardsIgnoreLine
            // @todo POSTNL-XXX toevoegen
            // @codingStandardsIgnoreLine
                __('Missing required parameters: %1', implode(', ',$requiredOrderParams))
            );
        }

        return $params;
    }

    /**
     * @param string $type
     * @param array  $params
     *
     * @return array
     */
    private function requiredOrderParamsMissing($type, $params)
    {
        $requiredList = $this->setRequiredList($type);

        $missing = array_filter($requiredList, function ($value, $key) use ($params) {
            $paramValue = isset($params[$key]) && !empty($params[$key]) ? $params[$key] : false;
            return !$paramValue && true == $value;
        }, \Zend\Stdlib\ArrayUtils::ARRAY_FILTER_USE_BOTH);

        return array_keys($missing);
    }

    /**
     * @param $type
     *
     * @return array
     */
    private function setRequiredList($type)
    {
        $list = [];
        foreach ($this->optionParams as $key => $value) {
            $list[$key] = $value[$type];
        }

        return $list;
    }

    /**
     * If you whant to store the param inside the tig_postnl_order table,
     * you need to give the keys the same name as the column names.
     *
     * @param $params
     *
     * @return array
     */
    private function formatParamData($params)
    {
        $productInfo = $this->productCodeAndType->get($params['type'], $params['option']);

        return [
            'quote_id'                     => isset($params['quote_id']) ? $params['quote_id'] : '',
            'delivery_date'                => isset($params['date']) ? $params['date'] : '',
            'expected_delivery_time_start' => isset($params['from']) ? $params['from'] : '',
            'expected_delivery_time_end'   => isset($params['to']) ? $params['to'] : '',
            'is_pakjegemak'                => $params['type'] == 'pickup' ? 1 : 0,
            'pg_location_code'             => isset($params['LocationCode']) ? $params['LocationCode'] : '',
            'pg_retail_network_id'         => isset($params['RetailNetworkID']) ? $params['RetailNetworkID'] : '',
            'pg_address'                   => $this->addExtraToAddress($params),
            'type'                         => $params['option'],
            'opening_hours'                => isset($params['OpeningHours']) ? $params['OpeningHours'] : '',
            'fee'                          => $this->feeCalculator->get($params),
            'product_code'                 => $productInfo['code'],
        ];
    }

    /**
     * @param $params
     *
     * @return bool
     * @throws PostnlException
     */
    private function addExtraToAddress($params)
    {
        if (!isset($params['address'])) {
            return false;
        }

        $params['address']['Name'] = isset($params['name']) ? $params['name'] : '';

        if (!isset($params['customerData'])) {
            throw new PostnlException(
            // @codingStandardsIgnoreLine
                __('Missing required parameters : customerData')
            );
        }

        $params['address']['customer'] = $params['customerData'];

        return $params['address'];
    }
}
