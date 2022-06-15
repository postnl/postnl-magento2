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
