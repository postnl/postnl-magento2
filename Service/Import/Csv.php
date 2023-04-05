<?php

namespace TIG\PostNL\Service\Import;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;

use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Model\Carrier\Tablerate;
use TIG\PostNL\Service\Import\Csv\FileParser;

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
