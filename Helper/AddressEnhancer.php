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
namespace TIG\PostNL\Helper;

use TIG\PostNL\Exception as PostnlException;

/**
 * Class Address
 *
 * @package TIG\PostNL\Helper
 */
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
            throw new PostnlException(
            // @codingStandardsIgnoreLine
                __('Unable to extract the housenumber, because the street data could not be found'),
                'POSTNL-0124'
            );
        }

        if (!isset($address['housenumber'])) {
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
        if (isset($address['street'][1]) && is_numeric($address['street'][1])) {
            return $address['housenumber'] = $address['street'][1];
        }

        $matched = preg_match(self::STREET_SPLIT_NAME_FROM_NUMBER, $address['street'][0], $result);
        if (!$matched) {
            throw new PostnlException(
            // @codingStandardsIgnoreLine
                __('Unable to extract the housenumber, could not find an number inside the street value'),
                'POSTNL-0124'
            );
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

        $address['housenumber']          = $houseNumberData['number'];
        $address['housenumberExtension'] = $houseNumberData['extension'];

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
                __('Invalid housnumber supplied: %1', $houseNumber),
                'POSTNL-0059'
            );
        }

        return [
            'number'    => (isset($results[1]) ? $results[1] : ''),
            'extension' => (isset($results[2]) ? $results[2] : '')
        ];
    }
}
