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

use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Model\Data\CountryInformation;
use Magento\Directory\Model\Data\CountryInformationFactory;
use Magento\Directory\Model\Data\RegionInformation;
use Magento\Directory\Model\Data\RegionInformationFactory;
use Magento\Directory\Model\ResourceModel\Country as CountryResource;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Country implements ContractInterface
{
    /** @var array  */
    private $countryList = [];

    /** @var null|int */
    private $websiteId = null;

    /**@var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var AllowedCountries */
    private $allowedCountries;

    /** @var CountryCollection */
    private $countryCollection;

    /** @var DirectoryHelper */
    private $directoryHelper;

    /** @var CountryInformationFactory */
    private $countryInformationFactory;

    /** @var RegionInformationFactory */
    private $regionInformationFactory;

    /**
     * @param ScopeConfigInterface      $scopeConfig
     * @param StoreManagerInterface     $storeManager
     * @param AllowedCountries          $allowedCountries
     * @param CountryCollection         $countryCollection
     * @param DirectoryHelper           $directoryHelper
     * @param CountryInformationFactory $countryInformationFactory
     * @param RegionInformationFactory  $regionInformationFactory
     */
    public function __construct(
        ScopeConfigInterface      $scopeConfig,
        StoreManagerInterface     $storeManager,
        AllowedCountries          $allowedCountries,
        CountryCollection         $countryCollection,
        DirectoryHelper           $directoryHelper,
        CountryInformationFactory $countryInformationFactory,
        RegionInformationFactory  $regionInformationFactory
    ) {
        $this->scopeConfig               = $scopeConfig;
        $this->storeManager              = $storeManager;
        $this->allowedCountries          = $allowedCountries;
        $this->countryCollection         = $countryCollection;
        $this->directoryHelper           = $directoryHelper;
        $this->countryInformationFactory = $countryInformationFactory;
        $this->regionInformationFactory  = $regionInformationFactory;
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
        $parts  = explode(',', $line);
        $pieces = array_map(function ($part) {
            return $this->validateSingle($part);
        }, $parts);

        if (in_array(false, $pieces, true)) {
            return false;
        }

        return implode(',', $pieces);
    }

    /**
     * @param int $websiteId
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
    }

    /**
     * @return array
     */
    public function getCountryList()
    {
        if (!empty($this->countryList)) {
            return $this->countryList;
        }

        $countriesInfo = $this->getCountriesInfo();

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
     * @return array
     */
    private function getCountriesInfo()
    {
        $countriesInfo     = [];
        $website           = $this->storeManager->getWebsite($this->websiteId);
        $storeLocale       = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_WEBSITE, $website);
        $allowedCountries  = $this->allowedCountries->getAllowedCountries(ScopeInterface::SCOPE_WEBSITE, $website);
        $countryCollection = $this->countryCollection->addFieldToFilter("country_id", ['in' => $allowedCountries]);
        $regions           = $this->directoryHelper->getRegionData();

        foreach ($countryCollection as $data) {
            $countryInfo = $this->setCountryInfo($data, $regions, $storeLocale);
            $countriesInfo[] = $countryInfo;
        }

        return $countriesInfo;
    }

    /**
     * @param CountryResource $country
     * @param array           $regions
     * @param string          $storeLocale
     *
     * @return CountryInformation
     */
    private function setCountryInfo($country, $regions, $storeLocale)
    {
        $countryId = $country->getCountryId();

        /** @var CountryInformation $countryInfo */
        $countryInfo = $this->countryInformationFactory->create();
        $countryInfo->setId($countryId);
        $countryInfo->setTwoLetterAbbreviation($country->getData('iso2_code'));
        $countryInfo->setThreeLetterAbbreviation($country->getData('iso3_code'));
        $countryInfo->setFullNameLocale($country->getName($storeLocale));
        $countryInfo->setFullNameEnglish($country->getName('en_US'));

        if (array_key_exists($countryId, $regions)) {
            $regionsInfo = [];
            foreach ($regions[$countryId] as $id => $regionData) {
                /** @var RegionInformation $regionInfo */
                $regionInfo = $this->regionInformationFactory->create();
                $regionInfo->setId($id);
                $regionInfo->setCode($regionData['code']);
                $regionInfo->setName($regionData['name']);
                $regionsInfo[] = $regionInfo;
            }
            $countryInfo->setAvailableRegions($regionsInfo);
        }

        return $countryInfo;
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
