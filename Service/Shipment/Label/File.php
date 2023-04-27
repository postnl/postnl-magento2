<?php

namespace TIG\PostNL\Service\Shipment\Label;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File as IoFile;

class File
{
    const TEMP_LABEL_FOLDER = 'PostNL' . DIRECTORY_SEPARATOR . 'templabel';
    const TEMP_LABEL_FILENAME = 'TIG_PostNL_temp.pdf';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var IoFile
     */
    private $ioFile;

    /**
     * @var array
     */
    private $fileList = [];

    /**
     * @param DirectoryList $directoryList
     * @param IoFile        $ioFile
     */
    public function __construct(
        DirectoryList $directoryList,
        IoFile $ioFile
    ) {
        $this->directoryList = $directoryList;
        $this->ioFile = $ioFile;
    }

    /**
     * @param $contents
     *
     * @return string
     * @throws \Exception
     */
    public function save($contents)
    {
        $filename = $this->reserveFilename();

        $this->ioFile->checkAndCreateFolder($this->getPath());
        $this->ioFile->write($filename, $contents);

        return $filename;
    }

    /**
     * Cleanup old files.
     */
    public function cleanup()
    {
        foreach ($this->fileList as $file) {
            // @codingStandardsIgnoreLine
            $this->ioFile->rm($file);
        }
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $tempFilePath = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . self::TEMP_LABEL_FOLDER;

        return $tempFilePath;
    }

    /**
     * @return string
     */
    public function reserveFilename()
    {
        $tempFilePath     = $this->getPath();
        $tempFileName     = sha1(microtime()) . '-' . time() . '-' . self::TEMP_LABEL_FILENAME;
        $filename         = $tempFilePath . DIRECTORY_SEPARATOR . $tempFileName;
        $this->fileList[] = $filename;

        return $filename;
    }
}
