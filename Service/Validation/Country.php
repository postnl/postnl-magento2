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

namespace TIG\PostNL\Service\Validation;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;

class Country implements ContractInterface
{
    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformation;

    /**
     * @var array
     */
    private $countryList = [];

    /**
     * @param CountryInformationAcquirerInterface $countryInformation
     */
    public function __construct(
        CountryInformationAcquirerInterface $countryInformation
    ) {
        $this->countryInformation = $countryInformation;
    }

    /**
     * Validate the data. Returns false when the
     *
     * @param $line
     *
     * @return bool|mixed
     */
    public function validate($line)
    {
        if (strpos($line, ',') !== false) {
            return $this->validateArray($line);
        }

        return $this->validateSingle($line);
    }

    /**
     * @param $line
     *
     * @return bool|string
     */
    private function validateSingle($line)
    {
        if ($line == '*') {
            return 0;
        }

        $countries = $this->getCountryList();

        if (!array_key_exists($line, $countries)) {
            return false;
        }

        return $countries[$line]['id'];
    }

    /**
     * @param $line
     *
     * @return bool|string
     */
    private function validateArray($line)
    {
        $parts = explode(',', $line);

        $pieces = array_map(function ($part) {
            return $this->validateSingle($part);
        }, $parts);

        if (in_array(false, $pieces, true)) {
            return false;
        }

        return implode(',', $pieces);
    }

    /**
     * @return array
     */
    public function getCountryList()
    {
        if (!empty($this->countryList)) {
            return $this->countryList;
        }

        $countriesInfo = $this->countryInformation->getCountriesInfo();

        foreach ($countriesInfo as $country) {
            $regions = $this->getRegions($country);

            $data = [
                'id'      => $country->getId(),
                'regions' => $regions,
            ];

            $this->countryList[$country->getTwoLetterAbbreviation()] = $data;
            $this->countryList[$country->getThreeLetterAbbreviation()] = $data;
        }

        return $this->countryList;
    }

    /**
     * @param CountryInformationInterface $country
     *
     * @return array
     */
    private function getRegions(CountryInformationInterface $country)
    {
        $regions = $country->getAvailableRegions();

        if ($regions === null) {
            return [];
        }

        $output = [];
        foreach ($regions as $region) {
            $output[$region->getCode()] = $region->getId();
            $output[$region->getName()] = $region->getId();
        }

        return $output;
    }
}
