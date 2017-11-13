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

namespace TIG\PostNL\Service\Import\Matrixrate;

use Magento\Framework\Filesystem\File\ReadInterface;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate;
use TIG\PostNL\Service\Import\ParseErrors;
use TIG\PostNL\Service\Wrapper\Store;
use TIG\PostNL\Test\Integration\Service\Import\IncorrectFormat;

class Data
{
    /**
     * @var Matrixrate
     */
    private $matrixrateResource;

    /**
     * @var Matrixrate\Collection
     */
    private $matrixrateCollection;

    /**
     * @var Row
     */
    private $rowFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var int
     */
    private $websiteId;

    /**
     * @var ParseErrors
     */
    private $parseErrors;

    /**
     * @var array
     */
    private $importData = [];

    /**
     * @var int
     */
    private $importedRows = 0;

    /**
     * @param Store                 $store
     * @param Matrixrate            $matrixrateResource
     * @param Matrixrate\Collection $matrixrateCollection
     * @param ParseErrors           $parseErrors
     * @param RowFactory            $rowFactory
     */
    public function __construct(
        Store $store,
        Matrixrate $matrixrateResource,
        Matrixrate\Collection $matrixrateCollection,
        ParseErrors $parseErrors,
        RowFactory $rowFactory
    ) {
        $this->matrixrateCollection = $matrixrateCollection;
        $this->matrixrateResource   = $matrixrateResource;
        $this->parseErrors          = $parseErrors;
        $this->rowFactory           = $rowFactory;

        $this->websiteId            = $store->getWebsiteId();
    }

    /**
     * @param ReadInterface $file
     *
     * @throws \Exception
     */
    public function import(ReadInterface $file)
    {
        $this->validateHeaders($file->readCsv());

        $this->connection = $this->matrixrateResource->getConnection();
        $this->connection->beginTransaction();

        try {
            $this->deleteData();
            $this->collectData($file);
            $this->saveToDatabase();
            $this->checkErrors();
            $this->connection->commit();
        } catch (\Exception $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param ReadInterface $file
     */
    private function collectData(ReadInterface $file)
    {
        $row = 0;
        /** @var \Magento\Framework\Filesystem\File\Read $line */
        while (($line = $file->readCsv()) !== false) {
            $this->parseRow(++$row, $line);

            $this->saveData();
        }
    }

    /**
     * @param int                 $rowNumber
     * @param ReadInterface|array $line
     */
    private function parseRow($rowNumber, $line)
    {
        /**
         * If the row is empty, the readCsv of ReadInterface will provide us with this array:
         *
         * $line = [0 => null];
         */
        if (empty($line) || (count($line) == 1 && $line[0] === null)) {
            return;
        }

        /** @var Row $row */
        $row = $this->rowFactory->create(['errorParser' => $this->parseErrors]);
        $data = $row->process($rowNumber, $line);

        if ($row->hasErrors()) {
            $this->parseErrors->addErrors($row->getErrors());
            return;
        }

        $this->importData[] = $data;
    }

    /**
     * @param $header
     *
     * @throws IncorrectFormat
     */
    private function validateHeaders($header)
    {
        if ($header === false || count($header) < 8) {
            // @codingStandardsIgnoreLine
            throw new IncorrectFormat(__('Invalid PostNL Matrix Rates File Format'), 'POSTNL-0194');
        }
    }

    /**
     * Delete all the data for the current website id
     */
    private function deleteData()
    {
        $this->matrixrateCollection->addFieldToFilter('website_id', $this->websiteId);
        $this->matrixrateCollection->clear();
        $this->matrixrateCollection->walk('delete');
        $this->importData = [];
    }

    /**
     * Import the data every 5000th row.
     */
    private function saveData()
    {
        $count = count($this->importData);
        if ($count < 5000) {
            return;
        }

        $this->saveToDatabase();
    }

    /**
     * Actually save the data to the database.
     */
    public function saveToDatabase()
    {
        $count = count($this->importData);
        $table = $this->matrixrateResource->getMainTable();
        $this->connection->insertMultiple($table, $this->importData);
        $this->importedRows += $count;
        $this->importData = [];
    }

    /**
     * @throws \TIG\PostNL\Service\Import\Exception
     */
    private function checkErrors()
    {
        if ($this->parseErrors->getErrorCount()) {
            throw new \TIG\PostNL\Service\Import\Exception(
                // @codingStandardsIgnoreLine
                __(
                    'File has not been imported. See the following list of errors: %1',
                    implode(', ' . PHP_EOL, $this->parseErrors->getErrors())
                ),
                'POSTNL-0196'
            );
        }
    }
}
