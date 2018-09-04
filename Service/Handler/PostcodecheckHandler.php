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
namespace TIG\PostNL\Service\Handler;

class PostcodecheckHandler
{
    /**
     * @param $params
     *
     * @return bool|mixed
     */
    public function convertResponse($params)
    {
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        if (!isset($params['errors']) && $this->validateParams($params[0], ['status', 'streetName', 'city'])) {
            return $params[0];
        }

        return false;
    }

    /**
     * @param $params
     *
     * @return array|bool
     */
    public function convertRequest($params)
    {
        if (!$this->validateParams($params, ['postcode', 'housenumber'])) {
            return false;
        }

        return [
            'postalcode'  => $params['postcode'],
            'housenumber' => $params['housenumber']
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
