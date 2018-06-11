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
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Source\LabelAndPackingslip\BarcodeValue;
use \Magento\Sales\Api\OrderRepositoryInterface;

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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var array
     */
    private $fileList = [];

    /**
     * @var $storeId
     */
    private $storeId = null;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $barcodeValue;

    /**
     * @param DirectoryList            $directoryList
     * @param IoFile                   $ioFile
     * @param PackingslipBarcode       $packingslipBarcode
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        DirectoryList $directoryList,
        IoFile $ioFile,
        PackingslipBarcode $packingslipBarcode,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->directoryList = $directoryList;
        $this->ioFile = $ioFile;
        $this->barcodeSettings = $packingslipBarcode;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function add($packingSlip, $shipment)
    {
        $this->storeId = $shipment->getStoreId();
        if (!$this->barcodeSettings->isEnabled($this->storeId)) {
            return $packingSlip;
        }

        $this->setFileName();
        $this->setBarcodeValue($shipment);

        // @codingStandardsIgnoreLine
        $pdf = new \Zend_Pdf();
        // @codingStandardsIgnoreLine
        $packingSlip = \Zend_Pdf::parse($packingSlip);
        /** @var \Zend_Pdf_Page $page */
        foreach ($packingSlip->pages as $page) {
            $pdf->pages[] = $this->addBarcodeToPage(clone $page);
        }

        $this->cleanup();
        return $pdf->render();
    }

    /**
     * @param \Zend_Pdf_Page $page
     *
     * @return \Zend_Pdf_Page $page
     */
    private function addBarcodeToPage($page)
    {
        $this->createBarcode();

        $postion      = $this->barcodeSettings->getPosition($this->storeId);
        // @codingStandardsIgnoreLine
        $barcodeImage = \Zend_Pdf_Image::imageWithPath($this->fileName);
        $page->drawImage($barcodeImage, $postion[0], $postion[1], $postion[2], $postion[3]);

        return $page;
    }

    /**
     * Creates the barcode as an temporary image.
     */
    private function createBarcode()
    {
        $barcodeOptions = [
            'text'            => $this->barcodeValue,
            'factor'          => '1',
            'drawText'        => $this->barcodeSettings->includeNumber($this->storeId),
            'backgroundColor' => $this->barcodeSettings->getBackgroundColor($this->storeId),
            'foreColor'       => $this->barcodeSettings->getFontColor($this->storeId),
        ];

        $type = $this->barcodeSettings->getType($this->storeId);
        // @codingStandardsIgnoreLine
        $imageResource = \Zend_Barcode::draw($type, 'image', $barcodeOptions, []);
        // @codingStandardsIgnoreLine
        imagejpeg($imageResource, $this->fileName, 100);
        // @codingStandardsIgnoreLine
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

    /**
     * @param ShipmentInterface $shipment
     */
    private function setBarcodeValue($shipment)
    {
        $this->barcodeValue = $shipment->getIncrementId();
        $valueType          = $this->barcodeSettings->getValue($this->storeId);

        if ($valueType == BarcodeValue::REFEENCE_TYPE_ORDER_ID) {
            $order = $this->orderRepository->get($shipment->getOrderId());
            $this->barcodeValue = $order->getIncrementId();
        }
    }

    private function setFileName()
    {
        $pathFile = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . static::TMP_BARCODE_PATH;
        $this->ioFile->checkAndCreateFolder($pathFile);

        $tempFileName     = sha1(microtime()) . '-' . time() . '-' . static::TMP_BARCODE_FILE;
        $this->fileName   = $pathFile . DIRECTORY_SEPARATOR . $tempFileName;
        $this->fileList[] = $this->fileName;
    }
}
