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
    const STREET_SPLIT_NAME_FROM_NUMBER = '/(?P<street>\D+) (?P<number>\d+)(?P<addition>\D*)/';

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
                    'message' => 'Unable to extract the house number, could not find a number inside the street value'
                ]
            ];
        }

        if ($result['street']) {
            $address['street'][0] = trim($result['street']);
        }

        if ($result['number']) {
            $address['housenumber'] = trim($result['number']);
            $address['housenumberExtension'] = trim($result['addition']);
        }

        return $address;
    }
}
