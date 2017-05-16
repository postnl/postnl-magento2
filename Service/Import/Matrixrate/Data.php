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

use Magento\Framework\DB\Transaction;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Filesystem\File\ReadInterface;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Test\Integration\Service\Import\IncorrectFormat;

class Data
{
    /**
     * @var \TIG\PostNL\Model\Carrier\ResourceModel\MatrixRate
     */
    private $matrixrateResource;

    /**
     * @var \TIG\PostNL\Model\Carrier\MatrixRateFactory
     */
    private $matrixrateFactory;
    /**
     * @var MatrixrateRepository
     */
    private $matrixrateRepository;

    public function __construct(
        \TIG\PostNL\Model\Carrier\MatrixRateFactory $matrixrateFactory,
        \TIG\PostNL\Model\Carrier\MatrixrateRepository $matrixrateRepository,
        \TIG\PostNL\Model\Carrier\ResourceModel\MatrixRate $matrixrateResource
    ) {
        $this->matrixrateResource = $matrixrateResource;
        $this->matrixrateFactory = $matrixrateFactory;
        $this->matrixrateRepository = $matrixrateRepository;
    }

    public function import(ReadInterface $file)
    {
        $this->validateHeaders($file->readCsv());

        $connection = $this->matrixrateResource->getConnection();
        $connection->beginTransaction();

        try {
            $this->importData($file);
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }

    private function importData(ReadInterface $file)
    {
        /** @var \Magento\Framework\Filesystem\File\Read $line */
        while(($line = $file->readCsv()) !== false) {
            $this->parseRow($line);
        }
    }

    private function parseRow($line)
    {
        if (empty($line)) {
            return;
        }

        $this->importRow($line);
    }

    private function importRow($line)
    {
        /** @var \TIG\PostNL\Model\Carrier\MatrixRate $model */
        $model = $this->matrixrateFactory->create();

        $model->setDestinyCountryId($model[0]);

        $this->matrixrateRepository->save($model);
    }

    private function validateHeaders($header)
    {
        if ($header === false || count($header) < 8) {
            throw new IncorrectFormat(__('Invalid PostNL Matrix Rates File Format'), 'POSTNL-0194');
        }
    }
}
