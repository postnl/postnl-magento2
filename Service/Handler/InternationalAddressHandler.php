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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
     * TODO: check possible response errors
     *
     * @param $params
     *
     * @return bool|mixed
     */
    public function convertResponse($params)
    {
        $params = $this->formatParams($params);

        if (empty($params)) {
            return false;
        }

        if (isset($params['errors']) || isset($params['fault']) || !isset($params[0])) {
            //@codingStandardsIgnoreLine
            $this->logger->critical(__('Error received getting address data from PostNL.'), $params);
            return 'error';
        }

        if ($this->validateParams($params[0], ['streetName', 'houseNumber', 'postalCode', 'cityName', 'countryName'])) {
            return count($params);
        }

        return false;
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
            'addressLine'  => trim($params['street']),
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
        $check = 0;
        foreach ($keysToContain as $key) {
            array_key_exists($key, $data)?: $check++;
        }

        return $check == 0;
    }
}
