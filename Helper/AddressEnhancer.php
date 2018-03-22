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
namespace TIG\PostNL\Helper;

use TIG\PostNL\Exception as PostnlException;

class AddressEnhancer
{
    const STREET_SPLIT_NAME_FROM_NUMBER = '/([^\d]+)\s?(.+)/i';
    const SPLIT_HOUSENUMBER_REGEX       = '#^([\d]+)(.*)#s';

    /** @var array */
    // @codingStandardsIgnoreLine
    protected $address = [];

    /**
     * @param $address
     */
    public function set($address)
    {
        $this->address = $this->appendHouseNumber($address);
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
            return ['error' => [
                        'code'    => 'POSTNL-0124',
                        'message' =>
                            'Unable to extract the house number, because the street data could not be found'
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
        $street = implode(' ', $address['street']);
        $matched = preg_match(self::STREET_SPLIT_NAME_FROM_NUMBER, $street, $result);
        if (!$matched) {
            return [
                'error' => [
                    'code'    => 'POSTNL-0124',
                    'message' => 'Unable to extract the house number, could not find an number inside the street value'
                ]
            ];
        }

        if (isset($result[1])) {
            $address['street'][0] = trim($result[1]);
        }

        if (isset($result[2])) {
            $address = $this->addHousenumberValues($address, $result[2]);
        }

        return $address;
    }

    /**
     * @param $address
     * @param $houseNumber
     *
     * @return mixed
     * @throws PostnlException
     */
    // @codingStandardsIgnoreLine
    protected function addHousenumberValues($address, $houseNumber)
    {
        $houseNumberData = $this->splitHousenumber($houseNumber);

        $address['housenumber']          = trim($houseNumberData['number']);
        $address['housenumberExtension'] = trim($houseNumberData['extension']);

        return $address;
    }

    /**
     * @param $houseNumber
     *
     * @return array
     * @throws PostnlException
     */
    // @codingStandardsIgnoreLine
    protected function splitHousenumber($houseNumber)
    {
        $matched = preg_match(self::SPLIT_HOUSENUMBER_REGEX, trim($houseNumber), $results);
        if (!$matched && !is_array($results)) {
            throw new PostnlException(
            // @codingStandardsIgnoreLine
                __('Invalid house number supplied: %1', $houseNumber),
                'POSTNL-0059'
            );
        }

        return [
            'number'    => (isset($results[1]) ? $results[1] : ''),
            'extension' => (isset($results[2]) ? $results[2] : '')
        ];
    }
}
