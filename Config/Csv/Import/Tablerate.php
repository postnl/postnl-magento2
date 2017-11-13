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
namespace TIG\PostNL\Config\Csv\Import;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

use TIG\PostNL\Model\ResourceModel\TablerateFactory;
use TIG\PostNL\Service\Import\Csv;

class Tablerate extends Value
{
    /**
     * @var TablerateFactory
     */
    private $tablerateFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Csv
     */
    private $csv;

    /**
     * @param Context               $context
     * @param Registry              $registry
     * @param ScopeConfigInterface  $config
     * @param TypeListInterface     $cacheTypeList
     * @param TablerateFactory      $tablerateFactory
     * @param StoreManagerInterface $storeManager
     * @param Csv                   $csv
     * @param AbstractResource      $resource
     * @param AbstractDb            $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        TablerateFactory $tablerateFactory,
        StoreManagerInterface $storeManager,
        Csv $csv,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->tablerateFactory = $tablerateFactory;
        $this->storeManager = $storeManager;
        $this->csv = $csv;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        /** @var \TIG\PostNL\Model\ResourceModel\Tablerate $tableRate */
        $tableRate = $this->tablerateFactory->create();
        $tablerateConditionName = $tableRate->getConditionName($this);

        $csvData = $this->getCsvData($this->getScopeId(), $tablerateConditionName);

        $tableRate->uploadAndImport($this, $csvData);

        return parent::afterSave();
    }

    /**
     * @param int        $scopeId
     * @param mixed|string $conditionName
     *
     * @return array
     */
    private function getCsvData($scopeId, $conditionName)
    {
        $csvData = [];

        // @codingStandardsIgnoreLine
        if (empty($_FILES['groups']['tmp_name']['tig_postnl']['fields']['import']['value'])) {
            return $csvData;
        }

        // @codingStandardsIgnoreLine
        $filePath = $_FILES['groups']['tmp_name']['tig_postnl']['fields']['import']['value'];

        $website = $this->storeManager->getWebsite($scopeId);
        $websiteId = $website->getId();

        $csvData = $this->csv->getData($filePath, $websiteId, $conditionName);

        return $csvData;
    }
}
