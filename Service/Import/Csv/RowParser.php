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
namespace TIG\PostNL\Service\Import\Csv;

use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\LocalizedException;

use TIG\PostNL\Exception as PostnlException;

class RowParser
{
    const COLUMN_COUNTRY = 0;
    const COLUMN_REGION = 1;
    const COLUMN_ZIP = 2;
    const COLUMN_CONDITION_VALUE = 3;
    const COLUMN_PRICE = 4;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @param CountryFactory $countryFactory
     * @param RegionFactory  $regionFactory
     */
    public function __construct(
        CountryFactory $countryFactory,
        RegionFactory $regionFactory
    ) {
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $websiteId
     * @param $conditionName
     * @param $conditionFullName
     *
     * @return array
     * @throws LocalizedException
     */
    public function parseRow($rowData, $rowCount, $websiteId, $conditionName, $conditionFullName)
    {
        if (count($rowData) < 5) { // @codingStandardsIgnoreLine
            throw new PostnlException(__('Invalid PostNL Table Rates File Format in Row #%1', $rowCount), 'POSTNL-0247');
        }

        $countryId = $this->getCountryId($rowData, $rowCount);
        $regionId = $this->getRegionId($rowData, $rowCount, $countryId);
        $zipCode = $this->getZipCode($rowData, $rowCount);
        $conditionValue = $this->getConditionValue($rowData, $rowCount, $conditionFullName);
        $price = $this->getPrice($rowData, $rowCount);

        return [
            'website_id' => $websiteId,
            'dest_country_id' => $countryId,
            'dest_region_id' => $regionId,
            'dest_zip' => $zipCode,
            'condition_name' => $conditionName,
            'condition_value' => $conditionValue,
            'price' => $price,
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     *
     * @return string
     * @throws LocalizedException
     */
    private function getCountryId($rowData, $rowCount)
    {
        $countryId = '0';
        $countryCode = $this->getColumnValue(self::COLUMN_COUNTRY, $rowData, $rowCount);

        if ($countryCode != '*' && $countryCode != '') {
            /** @var \Magento\Directory\Model\Country $country */
            $country   = $this->countryFactory->create();
            $country   = $country->loadByCode($countryCode);
            $countryId = $country->getId();
        }

        return $countryId;
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $countryId
     *
     * @return string
     * @throws LocalizedException
     */
    private function getRegionId($rowData, $countryId, $rowCount)
    {
        $regionId = '0';
        $regionCode = $this->getColumnValue(self::COLUMN_REGION, $rowData, $rowCount);

        if ($regionCode != '*' && $regionCode != '' && $countryId != '0') {
            /** @var \Magento\Directory\Model\Region $region */
            $region   = $this->regionFactory->create();
            $region   = $region->loadByCode($regionCode, $countryId);
            $regionId = $region->getId();
        }

        return $regionId;
    }

    /**
     * @param $rowData
     * @param $rowCount
     *
     * @return string
     * @throws LocalizedException
     */
    private function getZipCode($rowData, $rowCount)
    {
        $zipCode = $this->getColumnValue(self::COLUMN_ZIP, $rowData, $rowCount);

        if ($zipCode === '') {
            $zipCode = '*';
        }

        return $zipCode;
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $conditionFullName
     *
     * @return bool|float
     * @throws LocalizedException
     */
    private function getConditionValue($rowData, $rowCount, $conditionFullName)
    {
        $conditionValue = $this->getColumnValue(self::COLUMN_CONDITION_VALUE, $rowData, $rowCount);
        $formattedConditionValue = $this->parseToDecimal($conditionValue);

        if ($formattedConditionValue === false) {
            $message = __('Invalid %1 "%2" supplied in row #%3', $conditionFullName, $conditionValue, $rowCount);

            throw new PostnlException($message, 'POSTNL-0248');
        }

        return $formattedConditionValue;
    }

    /**
     * @param $rowData
     * @param $rowCount
     *
     * @return bool|float|string
     * @throws LocalizedException
     */
    private function getPrice($rowData, $rowCount)
    {
        $price = $this->getColumnValue(self::COLUMN_PRICE, $rowData, $rowCount);
        $formatedPrice = $this->parseToDecimal($price);

        if ($formatedPrice === false) {
            $message = __('Invalid Shipping Price "%1" supplied in row #%2', $price, $rowCount);
            throw new PostnlException($message, 'POSTNL-249');
        }

        return $formatedPrice;
    }

    /**
     * @param $column
     * @param $row
     * @param $rowCount
     *
     * @return string
     * @throws LocalizedException
     */
    private function getColumnValue($column, $row, $rowCount)
    {
        if (!array_key_exists($column, $row)) { // @codingStandardsIgnoreLine
            $message = __('Invalid PostNL Table Rates File Format in Row #%1', $rowCount);
            throw new PostnlException($message, 'POSTNL-0247');
        }

        return trim($row[$column]);
    }

    /**
     * @param $value
     *
     * @return bool|float
     */
    private function parseToDecimal($value)
    {
        $result = false;

        if (is_numeric($value) && $value >= 0) {
            $result = (double)sprintf('%.4F', $value);
        }

        return $result;
    }
}
