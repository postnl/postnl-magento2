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
namespace TIG\PostNL\Services\Import\Csv;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\File\ReadInterface;

/**
 * Class FileParser
 *
 * @package TIG\PostNL\Services\Import\Csv
 */
class FileParser
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var
     */
    private $rowParser;

    /**
     * @param RowParser $rowParser
     */
    public function __construct(RowParser $rowParser)
    {
        $this->rowParser = $rowParser;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        $hasErrors = false;

        if (!empty($this->getErrors())) {
            $hasErrors = true;
        }

        return $hasErrors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            'website_id',
            'dest_country_id',
            'dest_region_id',
            'dest_zip',
            'condition_name',
            'condition_value',
            'price',
        ];
    }

    /**
     * @param ReadInterface $file
     * @param               $websiteId
     * @param               $conditionName
     * @param               $conditionFullName
     * @param int           $rowLimit
     *
     * @return array
     */
    public function getRows($file, $websiteId, $conditionName, $conditionFullName, $rowLimit = 5000)
    {
        $parsedRows = [];
        $this->validateHeaders($file);

        try {
            $parsedRows = $this->parseRows($file, $websiteId, $conditionName, $conditionFullName, $rowLimit);
        } catch (LocalizedException $exception) {
            $this->errors[] = $exception->getMessage();
        }

        return $parsedRows;
    }

    /**
     * @param $file
     * @param $websiteId
     * @param $conditionName
     * @param $conditionFullName
     * @param $rowLimit
     *
     * @return array
     * @throws LocalizedException
     */
    private function parseRows($file, $websiteId, $conditionName, $conditionFullName, $rowLimit)
    {
        $currentRowCount = 1;
        $limitCount = 0;
        $parsedRows = [];
        $validatedRows = [];
        $fileLines = $this->getCsvRows($file);

        foreach ($fileLines as $line) {
            $currentRowCount++;

            $rowData = $this->rowParser
                ->parseRow($line, $currentRowCount, $websiteId, $conditionName, $conditionFullName);

            $validatedRows = $this->validateDuplicates($rowData, $validatedRows, $currentRowCount);

            $parsedRows[$limitCount][] = $rowData;
            $limitCount += (int)(($currentRowCount % $rowLimit) == 0);
        }

        return $parsedRows;
    }

    /**
     * @param ReadInterface $file
     *
     * @return array
     */
    private function getCsvRows($file)
    {
        $fileLines = [];
        while (false !== ($fileLine = $file->readCsv())) {
            $fileLines[] = $fileLine;
        }

        $fileLines = array_filter(
            $fileLines,
            function ($line) {
                return !empty($line);
            }
        );

        return $fileLines;
    }

    /**
     * @param ReadInterface $file
     *
     * @throws LocalizedException
     */
    private function validateHeaders($file)
    {
        $headers = $file->readCsv();

        if ($headers === false || count($headers) < 5) {
            throw new LocalizedException(__('Please correct Table Rates File Format.'));
        }
    }

    /**
     * @param array $rowData
     * @param array $validatedRows
     * @param int   $currentRowCount
     *
     * @return array
     * @throws LocalizedException
     */
    private function validateDuplicates($rowData, $validatedRows, $currentRowCount)
    {
        $rowKey = $this->getRowKey($rowData);

        if (array_key_exists($rowKey, $validatedRows)) {
            throw new LocalizedException(
                __('Row #%1 is a dupplicate of row #%2', $currentRowCount, $validatedRows[$rowKey])
            );
        }

        $validatedRows[$rowKey] = $currentRowCount;

        return $validatedRows;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getRowKey($data)
    {
        $destinationCountry = $data['dest_country_id'];
        $destinationRegion = $data['dest_region_id'];
        $destinationZip = $data['dest_zip'];
        $conditionValue = $data['condition_value'];

        $key = $destinationCountry . '-' . $destinationRegion . '-' . $destinationZip . '-' . $conditionValue;

        return $key;
    }
}
