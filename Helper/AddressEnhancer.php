<?php

namespace TIG\PostNL\Helper;

use TIG\PostNL\Config\Provider\Webshop as Config;
use TIG\PostNL\Exception as PostnlException;

class AddressEnhancer
{
    // @codingStandardsIgnoreLine
    const STREET_SPLIT_NAME_FROM_NUMBER = '/^(?P<street>\d*[\wäöüßÀ-ÖØ-öø-ÿĀ-Ž\d \'\‘\`\-\.]+)[,\s]+(?P<number>\d+)\s*(?P<addition>[\wäöüß\d\-\/]*)$/i';
    // @codingStandardsIgnoreLine
    const STREET_SPLIT_NUMBER_FROM_NAME = '/^(?P<number>\d+)\s*(?P<street>[\wäöüßÀ-ÖØ-öø-ÿĀ-Ž\d \'\‘\`\-\.]*)$/i';

    /** @var Config $config */
    private $config;

    /** @var array */
    // @codingStandardsIgnoreLine
    protected $address = [];

    /**
     * AddressEnhancer constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param $address
     *
     * @throws PostnlException
     */
    public function set($address)
    {
        $this->address = $address;

        if ((isset($this->address['country']) && $this->address['country'] !== 'NL') ||
            !$this->config->getIsAddressCheckEnabled() ||
            // If an address is parsed as a 1-liner, we still have to extract the housenumber
            !is_array($address['street']) ||
            !isset($address['street'][1])
        ) {
            $this->address = $this->appendHouseNumber($address);
        }

        if (empty($this->address['housenumber']) && isset($this->address['street'][1])) {
            $this->address['housenumber'] = $this->address['street'][1];
        }
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->address;
    }

    /**
     * @param $address
     *
     * @return mixed
     * @throws PostnlException
     */
    // @codingStandardsIgnoreLine
    protected function appendHouseNumber($address)
    {
        if (!isset($address['street'][0])) {
            return [
                'error' => [
                    'code'    => 'POSTNL-0124',
                    'message' => 'Unable to extract the house number, because the street data could not be found'
                ]
            ];
        }

        if (!isset($address['housenumber']) || !$address['housenumber']) {
            $address = $this->extractHousenumber($address);
        }

        return $address;
    }

    /**
     * @param $address
     *
     * @return mixed
     * @throws PostnlException
     */
    // @codingStandardsIgnoreLine
    protected function extractHousenumber($address)
    {
        $street = $address['street'];

        if (is_array($address['street'])) {
            $street  = implode(' ', $address['street']);
        } else {
            $street = (string)$street;
        }
        $street = trim($street);

        $matched = preg_match(self::STREET_SPLIT_NAME_FROM_NUMBER, $street, $result);
        if (!$matched) {
            $result = $this->extractStreetFromNumber($street);
        }

        if (isset($result['error'])) {
            $result = $this->extractIndividual($address, $result);

            return $result;
        }

        return $this->parseResult($result, $address);
    }

    /**
     * @param $street
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function extractStreetFromNumber($street)
    {
        $matched = preg_match(self::STREET_SPLIT_NUMBER_FROM_NAME, $street, $result);
        if (!$matched) {
            return [
                'error' => [
                    'code'    => 'POSTNL-0124',
                    'message' => 'Unable to extract the house number, could not find a number inside the street value'
                ]
            ];
        }

        return $result;
    }

    /**
     * @param $address
     * @param $result
     *
     * @return mixed
     * @throws PostnlException
     */
    // @codingStandardsIgnoreLine
    protected function extractIndividual($address, $result)
    {
        // @codingStandardsIgnoreLine
        if (count($address['street']) == 3) {
            $result['street']     = $address['street'][0];
            $result['number']     = $address['street'][1];
            $result['addition']   = $address['street'][2];
            $address['street'][1] = '';
            $address['street'][2] = '';
            unset($result['error']);
        }
        // @codingStandardsIgnoreLine
        if (count($address['street']) == 2) {
            $tmpAddress           = $this->extractHousenumber(['street' => [$address['street'][0]]]);
            $result['street']     = $address['street'][0];
            $result['number']     = isset($tmpAddress['housenumber']) ? $tmpAddress['housenumber'] : null;
            $result['addition']   = $address['street'][1];
            $address['street'][1] = '';
            unset($result['error']);
        }

        return !isset($result['error']) ? $this->parseResult($result, $address) : $result;
    }

    /**
     * @param $result
     * @param $address
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    protected function parseResult($result, $address)
    {
        if (!is_array($result)) {
            return $address;
        }

        if ($result['street']) {
            $address['street'][0] = trim($result['street']);
        }

        if ($result['number']) {
            $address['housenumber'] = trim($result['number']);
        }

        $address['housenumberExtension'] = '';
        if (isset($result['addition']) && $result['addition']) {
            $address['housenumberExtension'] = trim($result['addition']);
        }

        return $address;
    }
}
