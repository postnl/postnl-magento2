<?php

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
