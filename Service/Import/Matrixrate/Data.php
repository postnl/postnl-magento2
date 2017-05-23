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
use Magento\Store\Model\StoreManagerInterface;
use TIG\PostNL\Model\Carrier\MatrixrateFactory;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate;
use TIG\PostNL\Test\Integration\Service\Import\IncorrectFormat;

class Data
{
    /**
     * @var Matrixrate
     */
    private $matrixrateResource;

    /**
     * @var MatrixrateFactory
     */
    private $matrixrateFactory;

    /**
     * @var MatrixrateRepository
     */
    private $matrixrateRepository;

    /**
     * @var int
     */
    private $websiteId;

    /**
     * @var Matrixrate\Collection
     */
    private $matrixrateCollection;

    /**
     * @param StoreManagerInterface $storeManager
     * @param MatrixrateFactory     $matrixrateFactory
     * @param MatrixrateRepository  $matrixrateRepository
     * @param Matrixrate            $matrixrateResource
     * @param Matrixrate\Collection $matrixrateCollection
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        MatrixrateFactory $matrixrateFactory,
        MatrixrateRepository $matrixrateRepository,
        Matrixrate $matrixrateResource,
        Matrixrate\Collection $matrixrateCollection
    ) {
        $this->matrixrateFactory = $matrixrateFactory;
        $this->matrixrateResource = $matrixrateResource;
        $this->matrixrateRepository = $matrixrateRepository;
        $this->matrixrateCollection = $matrixrateCollection;

        $store           = $storeManager->getStore();
        $this->websiteId = $store->getWebsiteId();
    }

    /**
     * @param ReadInterface $file
     *
     * @throws \Exception
     */
    public function import(ReadInterface $file)
    {
        $this->validateHeaders($file->readCsv());

        $connection = $this->matrixrateResource->getConnection();
        $connection->beginTransaction();

        try {
            $this->deleteData();
            $this->importData($file);
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param ReadInterface $file
     */
    private function importData(ReadInterface $file)
    {
        /** @var \Magento\Framework\Filesystem\File\Read $line */
        while (($line = $file->readCsv()) !== false) {
            $this->parseRow($line);
        }
    }

    /**
     * @param $line
     */
    private function parseRow($line)
    {
        if (empty($line)) {
            return;
        }

        $this->importRow($line);
    }

    /**
     * @param $line
     */
    private function importRow($line)
    {
        /** @var \TIG\PostNL\Model\Carrier\Matrixrate $model */
        $model = $this->matrixrateFactory->create();

        $model->setWebsiteId($this->websiteId);
        $model->setDestinyCountryId($line[0]);
        $model->setDestinyRegionId($line[1]);
        $model->setDestinyZipCode($line[2]);
        $model->setWeight($line[3]);
        $model->setPrice($line[4]);
        $model->setQuantity($line[5]);
        $model->setParcelType($line[6]);
        $model->setPrice($line[7]);

        $this->matrixrateRepository->save($model);
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
    }
}
