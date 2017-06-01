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

use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Service\Import\ParseErrors;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\File\ReadInterface;

class FileParser
{
    private $validatedRows = [];

    /**
     * @var RowParser
     */
    private $rowParser;

    /**
     * @var ParseErrors
     */
    private $parseErrors;

    /**
     * @param RowParser   $rowParser
     * @param ParseErrors $parserErrors
     */
    public function __construct(
        RowParser $rowParser,
        ParseErrors $parserErrors
    ) {
        $this->rowParser   = $rowParser;
        $this->parseErrors = $parserErrors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (bool)$this->parseErrors->getErrorCount();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->parseErrors->getErrors();
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
        $currentRowCount = 1;
        $limitCount = 0;
        $parsedRows = [];

        $this->validateHeaders($file);
        $csvRows = $this->getCsvRows($file);

        foreach ($csvRows as $row) {
            $currentRowCount++;

            $rowData = $this->parseRow($row, $websiteId, $conditionName, $conditionFullName, $currentRowCount);

            $parsedRows[$limitCount][] = $rowData;
            $limitCount += (int)((($currentRowCount - 1) % $rowLimit) == 0);
        }

        return $parsedRows;
    }

    /**
     * @param $csvRow
     * @param $websiteId
     * @param $conditionName
     * @param $conditionFullName
     * @param $currentRowCount
     *
     * @return array
     * @throws LocalizedException
     */
    private function parseRow($csvRow, $websiteId, $conditionName, $conditionFullName, $currentRowCount)
    {
        $rowData = [];

        try {
            $rowData = $this->rowParser
                ->parseRow($csvRow, $currentRowCount, $websiteId, $conditionName, $conditionFullName);

            $this->validateDuplicates($rowData, $currentRowCount);
        } catch (PostnlException $exception) {
            $this->parseErrors->addError($exception->getMessage());
        }

        return $rowData;
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
        try {
            $headers = $file->readCsv();

            $this->validateHeaderFormat($headers);
        } catch (PostnlException $exception) {
            $this->parseErrors->addError($exception->getMessage());
        }
    }

    /**
     * @param $headers
     *
     * @throws LocalizedException
     */
    private function validateHeaderFormat($headers)
    {
        if ($headers === false || count($headers) < 5) {
            // @codingStandardsIgnoreLine
            throw new PostnlException(__('Invalid PostNL Table Rates File Format'), 'POSTNL-0246');
        }
    }

    /**
     * @param array $rowData
     * @param int   $currentRowCount
     *
     * @throws LocalizedException
     */
    private function validateDuplicates($rowData, $currentRowCount)
    {
        $destinationCountry = $rowData['dest_country_id'];
        $destinationRegion = $rowData['dest_region_id'];
        $destinationZip = $rowData['dest_zip'];
        $conditionValue = $rowData['condition_value'];

        $rowKey = $destinationCountry . '-' . $destinationRegion . '-' . $destinationZip . '-' . $conditionValue;

        if (array_key_exists($rowKey, $this->validatedRows)) {
            throw new PostnlException(
                __('Row #%1 is a dupplicate of row #%2', $currentRowCount, $this->validatedRows[$rowKey]),
                'POSTNL-0250'
            );
        }

        $this->validatedRows[$rowKey] = $currentRowCount;
    }
}
