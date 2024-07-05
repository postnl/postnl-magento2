<?php

namespace TIG\PostNL\Config\Debug;

use Laminas\Stdlib\ParametersInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use TIG\PostNL\Service\Import\ConfigImporter;

class Import extends Value
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ConfigImporter
     */
    private $importer;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        RequestInterface $request,
        ConfigImporter $importer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->importer = $importer;
        $this->request = $request;
    }

    /**
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $requestFiles = $this->request->getFiles();
        $files = $requestFiles->offsetGet('groups');
        if (!isset($files['developer_settings']['groups']['config_dump']['fields']['config_import'])) {
            return parent::beforeSave();
        }

        $fileName  = $files['developer_settings']['groups']['config_dump']['fields']['config_import']['value']['tmp_name'];
        if (empty($fileName)) {
            return parent::beforeSave();
        }
        $newConfig = \file_get_contents($fileName);
        $newConfig = $this->importer->readConfig($newConfig);
        if (!$newConfig) {
            throw new LocalizedException(__('Invalid configuration file provided.'));
        }
        $this->importer->updateConfigs($newConfig);

        // We do not save anything
        $this->setValue(null);
        return parent::beforeSave();
    }
}
