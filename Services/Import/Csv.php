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
namespace TIG\PostNL\Services\Import;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;

use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Model\Carrier\Tablerate;
use TIG\PostNL\Services\Import\Csv\FileParser;

class Csv
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var Tablerate
     */
    private $tablerate;

    /**
     * @param Filesystem $filesystem
     * @param FileParser $fileParser
     * @param Tablerate  $tablerate
     */
    public function __construct(
        Filesystem $filesystem,
        FileParser $fileParser,
        Tablerate $tablerate
    ) {
        $this->filesystem = $filesystem;
        $this->tablerate = $tablerate;
        $this->fileParser = $fileParser;
    }

    /**
     * @param $filePath
     * @param $websiteId
     * @param $conditionName
     *
     * @return array
     * @throws LocalizedException
     */
    public function getData($filePath, $websiteId, $conditionName)
    {
        $file = $this->getCsvFile($filePath);
        $conditionFullName = $this->tablerate->getCode('condition_name_short', $conditionName);
        $importData = $this->fileParser->getRows($file, $websiteId, $conditionName, $conditionFullName);

        $columns = $this->fileParser->getColumns();
        $records = [];

        foreach ($importData as $data) {
            $records[] = $data;
        }

        $file->close();

        $this->checkImportErrors();

        return [
            'columns' => $columns,
            'records' => $records
        ];
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

    /**
     * @throws LocalizedException
     */
    private function checkImportErrors()
    {
        if ($this->fileParser->hasErrors()) {
            // @codingStandardsIgnoreLine
            $error = __(
                'File has not been imported. See the following list of errors: %1',
                implode(" \n", $this->fileParser->getErrors())
            );

            throw new PostnlException($error, 'POSTNL-0196');
        }
    }
}
