<?php

namespace TIG\PostNL\Service\Handler;

use TIG\PostNL\Logging\Log;

class PostcodecheckHandler
{
    /**
     * @var Log
     */
    private $logger;

    /**
     * PostcodecheckHandler constructor.
     *
     * @param Log $logger
     */
    public function __construct(
        Log $logger
    ) {
        $this->logger = $logger;
    }

    /**
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
            $this->logger->critical(__('Error received getting postcode data from PostNL.'), $params);
            return 'error';
        }

        if ($this->validateParams($params[0], ['streetName', 'city']) || $this->validateParams($params[0], ['streetName', 'cityName'])) {
            return $params[0];
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
