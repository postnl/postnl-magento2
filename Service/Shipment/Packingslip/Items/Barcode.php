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
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PdfReaderException;
use TIG\PostNL\Config\Provider\PackingslipBarcode;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Source\LabelAndPackingslip\BarcodeValue;
use \Magento\Sales\Api\OrderRepositoryInterface;
use TIG\PostNL\Logging\Log;
use Zend\Barcode\Barcode as ZendBarcode;

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
     * @var Log
     */
    private $logger;

    /**
     * @param DirectoryList            $directoryList
     * @param IoFile                   $ioFile
     * @param PackingslipBarcode       $packingslipBarcode
     * @param OrderRepositoryInterface $orderRepository
     * @param Log                      $logger
     */
    public function __construct(
        DirectoryList $directoryList,
        IoFile $ioFile,
        PackingslipBarcode $packingslipBarcode,
        OrderRepositoryInterface $orderRepository,
        Log $logger
    ) {
        $this->directoryList = $directoryList;
        $this->ioFile = $ioFile;
        $this->barcodeSettings = $packingslipBarcode;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function add($packingSlip, $shipment)
    {
        $this->storeId = $shipment->getStoreId();
        if (!$this->barcodeSettings->isEnabled($this->storeId)) {
            return $packingSlip;
        }

        $this->getFileName();
        $this->getBarcodeValue($shipment);
        $this->createBarcode();

        $pdf = $this->loadPdfAndAddBarcode($packingSlip);

        $this->cleanup();
        return $pdf->Output('S');
    }

    /**
     * @return Fpdi
     */
    private function loadPdfAndAddBarcode($packingSlip){
        // @codingStandardsIgnoreLine
        $pdf = new Fpdi();
        try {
            $stream = StreamReader::createByString($packingSlip);
            $pageCount = $pdf->setSourceFile($stream);
        } catch (PdfReaderException $readerException) {
            $this->logger->error('[Service\Shipment\PackingSlip\Items\Barcode] Error while loading sourcefile: ' . $readerException->getMessage());
            return $pdf;
        }

        for($pageIndex = 0; $pageIndex < $pageCount; $pageIndex++) {
            try {
                $templateId = $pdf->importPage($pageIndex + 1);
                $pageSize   = $pdf->getTemplateSize($templateId);

                if ($pageSize['width'] > $pageSize['height']) {
                    $pdf->AddPage('L', $pageSize);
                } else {
                    $pdf->AddPage('P', $pageSize);
                }
                $pdf->useTemplate($templateId);

                $this->addBarcodeToPage($pdf, $pageSize);

            } catch (PdfParserException $fpdiException) {
                $this->logger->error('[Service\Shipment\PackingSlip\Items\Barcode] PdfParserException: ' . $fpdiException->getMessage());
            } catch (PdfReaderException $readerException) {
                $this->logger->error('[Service\Shipment\PackingSlip\Items\Barcode] ReaderException: ' . $readerException->getMessage());
            }
        }

        return $pdf;
    }

    /**
     * @param $units
     *
     * @return float
     */
    private function zendPdfUnitsToMM($units) {
        return ($units / 72) * 25.4;
    }

    /**
     * @param Fpdi $pdf
     *
     * @return void
     */
    private function addBarcodeToPage($pdf, $pageSize)
    {
        $position = $this->barcodeSettings->getPosition($this->storeId);

        // Zend_PDF used BOTTOMLEFT as 0,0 and every point was 1/72 inch
        $x = $this->zendPdfUnitsToMM($position[0]);
        $y = $pageSize['height'] - $this->zendPdfUnitsToMM($position[3]);
        $w = $this->zendPdfUnitsToMM($position[2]) - $x;
        $h = $this->zendPdfUnitsToMM($position[3]) - $this->zendPdfUnitsToMM($position[1]);

        // @codingStandardsIgnoreLine
        $pdf->Image($this->fileName, $x,$y, $w, $h);
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
        $imageResource = ZendBarcode::draw($type, 'image', $barcodeOptions, []);
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
    private function getBarcodeValue($shipment)
    {
        $this->barcodeValue = $shipment->getIncrementId();
        $valueType          = $this->barcodeSettings->getValue($this->storeId);

        if ($valueType == BarcodeValue::REFEENCE_TYPE_ORDER_ID) {
            $order = $this->orderRepository->get($shipment->getOrderId());
            $this->barcodeValue = $order->getIncrementId();
        }
    }

    private function getFileName()
    {
        $pathFile = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . static::TMP_BARCODE_PATH;
        $this->ioFile->checkAndCreateFolder($pathFile);

        $tempFileName     = sha1(microtime()) . '-' . time() . '-' . static::TMP_BARCODE_FILE;
        $this->fileName   = $pathFile . DIRECTORY_SEPARATOR . $tempFileName;
        $this->fileList[] = $this->fileName;
    }
}
