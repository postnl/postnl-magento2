<?php

namespace TIG\PostNL\Service\Import\Csv;

use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Exception as PostnlException;
use function __;
use function array_key_exists;
use function count;
use function is_numeric;
use function sprintf;
use function trim;

class RowParser
{
    public const COLUMN_COUNTRY = 0;
    public const COLUMN_REGION = 1;
    public const COLUMN_ZIP = 2;
    public const COLUMN_CONDITION_VALUE = 3;
    public const COLUMN_PRICE = 4;

    public function __construct(
        private readonly CountryFactory $countryFactory,
        private readonly RegionFactory $regionFactory
    ) {
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $websiteId
     * @param $conditionName
     * @param $conditionFullName
     *
     * @throws LocalizedException
     */
    public function parseRow($rowData, $rowCount, $websiteId, $conditionName, $conditionFullName): array
    {
        if (count($rowData) < 5) {
            throw new PostnlException(
                __('Invalid PostNL Table Rates File Format in Row #%1', $rowCount),
                'POSTNL-0247'
            );
        }

        $countryId = $this->getCountryId($rowData, $rowCount);

        return [
            'website_id' => $websiteId,
            'dest_country_id' => $countryId,
            'dest_region_id' => $this->getRegionId($rowData, $rowCount, $countryId),
            'dest_zip' => $this->getZipCode($rowData, $rowCount),
            'condition_name' => $conditionName,
            'condition_value' => $this->getConditionValue($rowData, $rowCount, $conditionFullName),
            'price' => $this->getPrice($rowData, $rowCount),
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     *
     * @return string
     * @throws LocalizedException
     */
    private function getCountryId($rowData, $rowCount): string
    {
        $countryId = '0';
        $countryCode = $this->getColumnValue(self::COLUMN_COUNTRY, $rowData, $rowCount);

        if ($countryCode !== '*' && $countryCode !== '') {
            /** @var Country $country */
            $country = $this->countryFactory->create();
            $country = $country->loadByCode($countryCode);
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
    private function getRegionId($rowData, $countryId, $rowCount): string
    {
        $regionId = '0';
        $regionCode = $this->getColumnValue(self::COLUMN_REGION, $rowData, $rowCount);

        if ($regionCode !== '*' && $regionCode !== '' && $countryId != '0') {
            /** @var Region $region */
            $region = $this->regionFactory->create();
            $region = $region->loadByCode($regionCode, $countryId);
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
    private function getZipCode($rowData, $rowCount): string
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
    private function getConditionValue($rowData, $rowCount, $conditionFullName): float|bool
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
    private function getPrice($rowData, $rowCount): float|bool
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
    private function getColumnValue($column, $row, $rowCount): string
    {
        if (!array_key_exists($column, $row)) {
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
    private function parseToDecimal($value): float|bool
    {
        $result = false;

        if (is_numeric($value) && $value >= 0) {
            $result = (float) sprintf('%.4F', $value);
        }

        return $result;
    }
}
