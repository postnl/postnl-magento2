<?php

namespace TIG\PostNL\Unit\Controller\Adminhtml;

use TIG\PostNL\Controller\Adminhtml\PdfDownload;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;
use TIG\PostNL\Test\TestCase;

class PdfDownloadTest extends TestCase
{
    protected $instanceClass = PdfDownload::class;

    /**
     * @return array
     */
    public function generateLabelProvider()
    {
        return [
            'type is shippinglabel' => [
                PdfDownload::FILETYPE_SHIPPINGLABEL,
                'label generator'
            ],
            'type is packingslip' => [
                PdfDownload::FILETYPE_PACKINGSLIP,
                'packingslip generator'
            ],
            'type is other' => [
                'other',
                ['some label']
            ],
        ];
    }

    /**
     * @param $filetype
     * @param $expected
     *
     * @dataProvider generateLabelProvider
     */
    public function testGenerateLabel($filetype, $expected)
    {
        $labelGenerateMock = $this->getFakeMock(LabelGenerate::class)->setMethods(['run'])->getMock();
        $labelGenerateMock->method('run')->willReturn('label generator');

        $packingslipGenerateMock = $this->getFakeMock(PackingslipGenerate::class)->setMethods(['run'])->getMock();
        $packingslipGenerateMock->method('run')->willReturn('packingslip generator');

        $instance = $this->getInstance([
            'labelGenerator' => $labelGenerateMock,
            'packingslipGenerator' => $packingslipGenerateMock
        ]);

        $result = $this->invokeArgs('generateLabel', [['some label'], $filetype], $instance);
        $this->assertEquals($expected, $result);
    }
}
