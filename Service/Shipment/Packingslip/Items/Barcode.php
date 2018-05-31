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
namespace TIG\PostNL\Service\Shipment\Packingslip\Items;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File as IoFile;
use TIG\PostNL\Config\Provider\PackingslipBarcode;

class Barcode implements ItemsInterface
{
    const TMP_BARCODE_PATH = 'PostNL' . DIRECTORY_SEPARATOR . 'tempbarcode';
    const TMP_BARCODE_FILE = 'TIG_PostNL_temp.jpeg';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var IoFile
     */
    private $ioFile;

    /**
     * @var PackingslipBarcode
     */
    private $barcodeSettings;

    /**
     * @var array
     */
    private $fileList = [];

    /**
     * @var $storeId
     */
    private $storeId = null;

    /**
     * @var
     */
    private $fileName;

    /**
     * @param DirectoryList      $directoryList
     * @param IoFile             $ioFile
     * @param PackingslipBarcode $packingslipBarcode
     */
    public function __construct(
        DirectoryList $directoryList,
        IoFile $ioFile,
        PackingslipBarcode $packingslipBarcode
    ) {
        $this->directoryList = $directoryList;
        $this->ioFile = $ioFile;
        $this->barcodeSettings = $packingslipBarcode;
    }

    /**
     * @inheritdoc
     */
    public function add($packingSlip, $shipment)
    {
        $this->storeId = $shipment->getStoreId();
        $this->setFileName();

        if ($packingSlip instanceof \Zend_Pdf) {
            $packingSlip = $packingSlip->render();
        }

        // @codingStandardsIgnoreLine
        $pdf = new \Zend_Pdf();

        $packingSlip = \Zend_Pdf::parse($packingSlip);
        /** @var \Zend_Pdf_Page $page */
        foreach ($packingSlip->pages as $page) {



            $pdf->pages[] = clone $page;
        }
    }

    /**
     * @param $barcode
     */
    private function createBarcode($barcode)
    {
        $barcodeOptions = [
            'text'      => $barcode,
            'barHeight' => $this->barcodeSettings->getHeight($this->storeId),
            'factor'    => '1',
            'drawText'  => $this->barcodeSettings->includeNumber($this->storeId)
        ];

        $type = $this->barcodeSettings->getType($this->storeId);
        $imageResource = \Zend_Barcode::draw($type, 'image', $barcodeOptions, []);

        imagejpeg($imageResource, $this->fileName, 100);
        imagedestroy($imageResource);
    }

    /**
     * Cleanup old files.
     */
    private function cleanup()
    {
        foreach ($this->fileList as $file) {
            // @codingStandardsIgnoreLine
            $this->ioFile->rm($file);
        }
    }

    private function setFileName()
    {
        $pathFile = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . static::TMP_BARCODE_PATH;
        $this->ioFile->checkAndCreateFolder($pathFile);

        $tempFileName     = sha1(microtime()) . '-' . time() . '-' . self::TMP_BARCODE_FILE;
        $this->fileName   = $pathFile . DIRECTORY_SEPARATOR . $tempFileName;
        $this->fileList[] = $this->fileName;
    }
}
