<?php

// @codingStandardsIgnoreFile
namespace TIG\PostNL\Helper\DeliveryOptions;

use Laminas\Stdlib\ArrayUtils;
use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Service\Order\FeeCalculator;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipment\ProductOptions;

class OrderParams
{
    private $optionParams = [
        'quote_id'                     => [
            'pickup'   => true,
            'delivery' => true,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'delivery_date'                => [
            'pickup'   => true,
            'delivery' => true,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'expected_delivery_time_start' => [
            'pickup'   => false,
            'delivery' => true,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'expected_delivery_time_end'   => [
            'pickup'   => false,
            'delivery' => true,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'is_pakjegemak'                => [
            'pickup'   => true,
            'delivery' => false,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'pg_location_code'             => [
            'pickup'   => true,
            'delivery' => false,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'pg_retail_network_id'         => [
            'pickup'   => true,
            'delivery' => false,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
        'pg_address'                   => [
            'pickup'   => true,
            'delivery' => false,
            'fallback' => false,
            'EPS'      => false,
            'GP'       => false
        ],
    ];

    /**
     * @var FeeCalculator
     */
    private $feeCalculator;

    /**
     * @var ProductInfo
     */
    private $productInfo;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @param FeeCalculator  $feeCalculator
     * @param ProductInfo    $productInfo
     * @param ProductOptions $productOptions
     */
    public function __construct(
        FeeCalculator $feeCalculator,
        ProductInfo $productInfo,
        ProductOptions $productOptions
    ) {
        $this->feeCalculator  = $feeCalculator;
        $this->productInfo    = $productInfo;
        $this->productOptions = $productOptions;
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
        $params              = array_merge($params, $this->getAcInformation($params));
        $requiredOrderParams = $this->requiredOrderParamsMissing($type, $params);

        if (!empty($requiredOrderParams)) {
            throw new PostnlException(
            // @todo POSTNL-XXX toevoegen
                __('Missing required parameters: %1', implode(', ', $requiredOrderParams))
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
        }, ArrayUtils::ARRAY_FILTER_USE_BOTH);

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

        if ($type === 'Letterbox Package') {
            return $list;
        }

        if ($type === 'Boxable Packet') {
            return $list;
        }

        foreach ($this->optionParams as $key => $value) {
            $list[$key] = $value[$type];
        }

        return $list;
    }

    /**
     * If you want to store the param inside the tig_postnl_order table,
     * you need to give the keys the same name as the column names.
     *
     * @param $params
     *
     * @return array
     * @throws PostnlException
     */
    private function formatParamData($params)
    {
        $option = $this->getOption($params);

        $productInfo = $this->productInfo->get($params['type'], $option, $params['address']);

        return [
            'quote_id'                     => isset($params['quote_id']) ? $params['quote_id'] : '',
            'delivery_date'                => isset($params['date']) ? $params['date'] : '',
            'expected_delivery_time_start' => isset($params['from']) ? $params['from'] : '',
            'expected_delivery_time_end'   => isset($params['to']) ? $params['to'] : '',
            'is_pakjegemak'                => $params['type'] == 'pickup' ? 1 : 0,
            'pg_location_code'             => isset($params['LocationCode']) ? $params['LocationCode'] : '',
            'pg_retail_network_id'         => isset($params['RetailNetworkID']) ? $params['RetailNetworkID'] : '',
            'pg_address'                   => $this->addExtraToAddress($params),
            'type'                         => $option,
            'opening_hours'                => isset($params['OpeningHours']) ? $params['OpeningHours'] : '',
            'fee'                          => $this->feeCalculator->get($params) + $this->feeCalculator->statedAddressOnlyFee($params),
            'product_code'                 => $productInfo['code'],
            'stated_address_only'          => isset($params['stated_address_only']) ? $params['stated_address_only'] : false,
            'country'                      => $params['country']
        ];
    }

    /**
     * Determine the option
     *
     * @param $params
     *
     * @return mixed|string
     */
    private function getOption($params)
    {
        $option = isset($params['option']) ? $params['option'] : 'Daytime';

        if (!isset($params['option']) && $params['type'] === 'EPS') {
            $option = $params['type'];
        }

        if (!isset($params['option']) && $params['type'] === 'GP') {
            $option = $params['type'];
        }

        if(!array_key_exists('country', $params)) {
            return $option;
        }

        if (!isset($params['option']) && $params['type'] === 'fallback' && $params['country'] == 'NL') {
            $option = 'Daytime';
        }

        if (!isset($params['option']) && $params['type'] === 'Boxable Packet' && $params['country'] !== 'NL') {
            $option = 'boxable_packets';
        }

        if (!isset($params['option']) && $params['type'] === 'fallback' && $params['country'] !== 'NL' && in_array($params['country'], EpsCountries::ALL)) {
            $option = 'EPS';
        }

        if (!isset($params['option']) && $params['type'] === 'fallback' && $params['country'] !== 'NL' && !in_array($params['country'], EpsCountries::ALL)) {
            $option = 'GP';
        }

        if (!isset($params['option']) && $params['type'] === 'Letterbox Package' && $params['country'] == 'NL') {
            $option = 'letterbox_package';
        }

        return $option;
    }

    /**
     * Get the AgentCodes for specific type consignments
     *
     * formatParamData
     *
     * @param $params
     *
     * @return array
     */
    private function getAcInformation($params)
    {
        $type = strtolower($params['type']);

        if (isset($params['product_code']) && strlen($params['product_code']) > 4) {
            $type .= '-' . substr($params['product_code'], 0, 1);
        }

        $acOptions = $this->productOptions->getByType($type);
        if (!$acOptions) {
            return [];
        }

        return [
            'ac_information' => $acOptions
        ];
    }

    /**
     * @param $params
     *
     * @return array|bool
     * @throws PostnlException
     */
    private function addExtraToAddress($params)
    {
        if (!isset($params['address'])) {
            return false;
        }

        if ($params['type'] == 'fallback') {
            $params['customerData'] = $params['address'];
        }

        if (is_array($params['address'])) {
            $params['address']['Name'] = isset($params['name']) ? $params['name'] : '';
        }

        if ($params['type'] == ProductInfo::TYPE_PICKUP && !isset($params['customerData'])) {
            throw new PostnlException(
                __('Missing required parameters: customerData')
            );
        }

        if ($params['type'] !== ProductInfo::TYPE_PICKUP) {
            return false;
        }

        $params['address']['customer'] = isset($params['customerData']) ? $params['customerData'] : $params['address'];

        return $params['address'];
    }
}
