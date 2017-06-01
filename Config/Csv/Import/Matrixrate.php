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
        /** @var \Zend\Stdlib\Parameters $requestFiles */
        $requestFiles = $this->request->getFiles();
        $files = $requestFiles->offsetGet('groups');

        if (!isset($files['tig_postnl']) || !isset($files['tig_postnl']['fields']['matrixrate_import'])) {
            return parent::beforeSave();
        }

        $fileName = $files['tig_postnl']['fields']['matrixrate_import']['value']['tmp_name'];

        if (empty($fileName)) {
            return parent::afterSave();
        }

        $file = $this->getCsvFile($fileName);
        $this->matrixrateData->import($file);
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
