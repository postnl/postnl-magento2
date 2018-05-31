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

class Barcode implements ItemsInterface
{
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
     * @inheritdoc
     */
    public function add($packingSlip, $shipment)
    {
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

    private function createBarcode($barcode)
    {
        $barcodeOptions = [
            'text'      => $barcode,
            'barHeight' => '50',
            'factor'    => '1',
            'drawText'  => true
        ];

        $imageResource = \Zend_Barcode::draw('code128', 'image', $barcodeOptions, []);


    }
}
