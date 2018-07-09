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
