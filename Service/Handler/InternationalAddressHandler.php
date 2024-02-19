<?php

namespace TIG\PostNL\Service\Handler;

use TIG\PostNL\Logging\Log;

class InternationalAddressHandler
{
    /** @var Log */
    private $logger;

    /**
     * @param Log $logger
     */
    public function __construct(
        Log $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param $params
     * @param $input
     *
     * @return array
     */
    public function convertResponse($params, $input)
    {
        $params = $this->formatParams($params);

        if (empty($params)) {
            return [200, [
                'addressCount' => 0,
                'addressMatchesFirst' => false,
                'message' => __('Sorry, we could not validate your address. Please check if the correct address has been filled.')
            ]];
        }

        if (isset($params['errors']) || isset($params['fault']) || !isset($params[0])) {
            //@codingStandardsIgnoreLine
            $this->logger->critical(__('Error received getting address data from PostNL.'), $params);
            return [500, [
                'message' => 'International address check response validation failed'
            ]];
        }

        if (!$this->validateParams($params[0], ['streetName', 'houseNumber', 'postalCode', 'cityName', 'countryName'])) {
            $this->logger->critical(__('Error received getting address data from PostNL.'), $params);
            return [500, [
                'message' => 'International address check response validation failed'
            ]];
        }

        $addressMatch = $this->doAdressesMatch($params[0], $input);
        return [200, [
            'addressCount' => count($params),
            'addressMatchesFirst' => $addressMatch,
            'message' => $addressMatch ? __('Your address is valid!') : __('Your address does not match our records, please select one of the addresses below'),
            'addresses' => $params
        ]];
    }

    /**
     * Strips Magento Fields from formattedAddress
     *
     * @param $address
     * @return mixed
     */
    protected function stripFormattedAddress($address)
    {
        $strippedAddress = $address['formattedAddress'];
        for ($i = count($strippedAddress) - 1; $i > 0; $i--) {
            if (in_array($strippedAddress[$i], [
                $address['cityName'],
                $address['countryName'],
                $address['postalCode'],
                $address['postalCode'] . ' ' . $address['cityName'],
                $address['cityName'] . ' ' . $address['postalCode']
            ])) {
                unset($strippedAddress[$i]);
                continue;
            }
            break;
        }
        return $strippedAddress;
    }


    /**
     * @param $params
     *
     * @return array
     */
    private function formatParams($params)
    {
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        if (!is_array($params)) {
            $params = [$params];
        }

        foreach ($params as &$param) {
            if (!isset($param['formattedAddress'])) {
                continue;
            }

            $param['strippedAddress'] = $this->stripFormattedAddress($param);
        }

        return $params;
    }

    /**
     * @param $params
     *
     * @return array|bool
     */
    public function convertRequest($params)
    {
        if (!$this->validateParams($params, ['street', 'postcode', 'city', 'country'])) {
            return false;
        }

        return [
            'addressLine' => trim($params['street'], "\t\r\n\0\x0, "),
            'postalCode' => $params['postcode'],
            'cityName' => $params['city'],
            'countryIso' => $params['country'],
        ];
    }

    /**
     * @param $params
     * @param $keysToContain
     *
     * @return bool
     */
    public function validateParams($params, $keysToContain)
    {
        if (!is_array($params)) {
            return false;
        }

        if (!$this->checkKeys($params, $keysToContain)) {
            return false;
        }

        return true;
    }

    /**
     * @param $data
     * @param $keysToContain
     *
     * @return bool
     */
    private function checkKeys($data, $keysToContain)
    {
        return empty(array_diff($keysToContain, array_keys($data)));
    }

    /**
     * @param $data
     * @param $input
     * @param $keysToContain
     *
     * @return bool
     */
    private function doAdressesMatch($data, $input)
    {
        if ($data['cityName'] !== $input['cityName']) {
            return false;
        }
        if ($data['postalCode'] !== $input['postalCode']) {
            return false;
        }
        if ($data['countryIso2'] !== $input['countryIso']) {
            return false;
        }
        if (strtolower(implode(', ', $data['strippedAddress'])) !== strtolower($input['addressLine'])) {
            return false;
        }
        return true;
    }
}
