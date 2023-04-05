<?php

namespace TIG\PostNL\Config\Csv\Import;

use Laminas\Stdlib\ParametersInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use TIG\PostNL\Service\Import\Matrixrate\Data;

class Matrixrate extends Value
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Data
     */
    private $matrixrateData;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Matrixrate constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param ScopeConfigInterface  $config
     * @param TypeListInterface     $cacheTypeList
     * @param Filesystem            $filesystem
     * @param Data                  $matrixrateData
     * @param RequestInterface      $request
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Filesystem $filesystem,
        Data $matrixrateData,
        RequestInterface $request,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);

        $this->filesystem = $filesystem;
        $this->matrixrateData = $matrixrateData;
        $this->request = $request;
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        /** @var ParametersInterface $requestFiles */
        $requestFiles = $this->request->getFiles();
        $files        = $requestFiles->offsetGet('groups');
        if (!isset($files['tig_postnl']) || !isset($files['tig_postnl']['fields']['matrixrate_import'])) {
            return parent::beforeSave();
        }

        $websiteId = $this->request->getParam('website', 0);
        $fileName  = $files['tig_postnl']['fields']['matrixrate_import']['value']['tmp_name'];
        if (empty($fileName)) {
            return parent::afterSave();
        }

        $file = $this->getCsvFile($fileName);
        $this->matrixrateData->import($file, $websiteId);
        $file->close();

        return parent::afterSave();
    }

    /**
     * @param string $filePath
     *
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     */
    private function getCsvFile($filePath)
    {
        $tmpDirectory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($filePath);
        return $tmpDirectory->openFile($path);
    }
}
