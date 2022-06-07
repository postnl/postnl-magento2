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
namespace TIG\PostNL\Test\Unit\Service\Shipment\Packingslip;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use TIG\PostNL\Service\Shipment\Packingslip\Generate;
use TIG\PostNL\Test\TestCase;

class GenerateTest extends TestCase
{
    public $instanceClass = Generate::class;

    /**
     * @param $pdfFiles
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::pdfLabelFiles
     */
    public function testRun($pdfFiles)
    {
        $decodedPdfFiles = [];

        foreach ($pdfFiles as $file) {
            $decodedPdfFiles[] = base64_decode($file);
        }

        $instance = $this->getInstance();
        $result = $instance->run($decodedPdfFiles);
        $this->assertIsString($result);
    }

    /**
     * @param $pdfFiles
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::pdfLabelFiles
     */
    public function testAddLabelToPdf($pdfFiles)
    {
        $instance = $this->getInstance();
        $pdf = new Fpdi();

        foreach ($pdfFiles as $file) {
            $decodedPdfFile = base64_decode($file);
            $pdf = $this->invokeArgs('addLabelToPdf', [$decodedPdfFile, $pdf], $instance);
        }

        $pdfFinal = new Fpdi();
        $pdfFinalReader = StreamReader::createByString($pdf->Output('S'));
        $count = $pdfFinal->setSourceFile($pdfFinalReader);

        $this->assertCount($count, $pdfFiles);
    }
}
