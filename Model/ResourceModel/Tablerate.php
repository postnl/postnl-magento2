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
namespace TIG\PostNL\Model\ResourceModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQueryFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

use TIG\PostNL\Exception as PostnlException;

class Tablerate extends AbstractDb
{
    /**
     * @var ScopeConfigInterface
     */
    private $coreConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RateQueryFactory
     */
    private $rateQueryFactory;

    /**
     * @param Context               $context
     * @param LoggerInterface       $logger
     * @param ScopeConfigInterface  $coreConfig
     * @param StoreManagerInterface $storeManager
     * @param RateQueryFactory      $rateQueryFactory
     * @param null|string           $connectionName
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ScopeConfigInterface $coreConfig,
        StoreManagerInterface $storeManager,
        RateQueryFactory $rateQueryFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->coreConfig = $coreConfig;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->rateQueryFactory = $rateQueryFactory;
    }

    /**
     * Constructor defining the resource model table and primary key
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('tig_postnl_tablerate', 'entity_id');
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param RateRequest $request
     *
     * @return array|bool
     * @throws LocalizedException
     */
    public function getRate(RateRequest $request)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select = $select->from($this->getMainTable());

        /** @var RateQuery $rateQuery */
        $rateQuery = $this->rateQueryFactory->create(['request' => $request]);
        $rateQuery->prepareSelect($select);
        $bindings = $rateQuery->getBindings();
        $result = $connection->fetchRow($select, $bindings);

        if ($result && $result['dest_zip'] == '*') {
            $result['dest_zip'] = '';
        }

        return $result;
    }

    /**
     * @param DataObject|Value $object
     * @param array            $importData
     *
     * @return $this
     * @throws LocalizedException
     */
    public function uploadAndImport(DataObject $object, $importData)
    {
        if (empty($importData)) {
            return $this;
        }

        try {
            $this->deleteByCondition($object);
            $this->importData($importData);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new PostnlException(__('An error occurred while importing the table rates.'), 'POSTNL-0251');
        }
        return $this;
    }

    /**
     * @param DataObject $object
     *
     * @return mixed|string
     */
    public function getConditionName(DataObject $object)
    {
        $conditionName = $object->getData('groups/tig_postnl/fields/condition_name/value');

        if ($object->getData('groups/tig_postnl/fields/condition_name/inherit') == '1') {
            $conditionName = (string)$this->coreConfig->getValue('carriers/tig_postnl/condition_name', 'default');
        }

        return $conditionName;
    }

    /**
     * @param DataObject|Value $object
     *
     * @throws LocalizedException
     */
    private function deleteByCondition(DataObject $object)
    {
        $website = $this->storeManager->getWebsite($object->getScopeId());
        $websiteId = $website->getId();
        $condition = ['website_id = ?' => $websiteId, 'condition_name = ?' => $this->getConditionName($object)];

        $connection = $this->getConnection();
        $connection->beginTransaction();
        $connection->delete($this->getMainTable(), $condition);
        $connection->commit();
    }

    /**
     * @param array $data
     */
    private function importData(array $data)
    {
        $columns = $data['columns'];
        $records = $data['records'];

        if (!empty($columns) && !empty($records)) {
            array_walk($records, [$this, 'saveImportData'], $columns);
        }
    }

    /**
     * @param $records
     * @param $index
     * @param $columns
     *
     * @throws LocalizedException
     */
    // @codingStandardsIgnoreLine
    private function saveImportData($records, $index, $columns)
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            $this->getConnection()->insertArray($this->getMainTable(), $columns, $records);
        } catch (\Exception $exception) {
            $connection->rollback();
            $this->logger->critical($exception);
            throw new PostnlException(__('An error occurred while importing the table rates.'), 'POSTNL-0251');
        }

        $connection->commit();
    }
}
