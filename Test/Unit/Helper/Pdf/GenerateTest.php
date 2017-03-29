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
namespace TIG\PostNL\Test\Unit\Helper\Pdf;

use Magento\Framework\Filesystem\Io\File;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\Pdf\Fpdf;
use TIG\PostNL\Helper\Pdf\Generate;
use TIG\PostNL\Helper\Pdf\Positions;
use TIG\PostNL\Model\ShipmentLabel;
use TIG\PostNL\Test\TestCase;

class GenerateTest extends TestCase
{
    protected $instanceClass = Generate::class;

    /**
     * @param $pdfFiles
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::pdfLabelFiles
     */
    /*public function testGet($pdfFiles)
    {
        $shipmentLabels = [];

        foreach ($pdfFiles as $pdfLabel) {
            $shipmentLabels[] = $this->getShipmentLabelMock($pdfLabel);
        }

        if (count($shipmentLabels) == 1) {
            $shipmentLabels = $shipmentLabels[0];
        }

        $positionsMock = $this->getFakeMock(Positions::class);
        $positionsMock->setMethods(null);
        $positionsMock = $positionsMock->getMock();

        $webshopMock = $this->getFakeMock(Webshop::class);
        $webshopMock->setMethods(null);
        $webshopMock = $webshopMock->getMock();

        $fpdfMock = $this->getMockBuilder(Fpdf::class);
        $fpdfMock->setMethods(['addLabel']);
        $fpdfMock->setConstructorArgs(['positions' => $positionsMock, 'webshop' => $webshopMock]);
        $fpdfMock = $fpdfMock->getMock();

        $addLabelExpects = $fpdfMock->expects($this->exactly(count($pdfFiles)));
        $addLabelExpects->method('addLabel');

        $instance = $this->getInstance(['Fpdf' => $fpdfMock]);
        $result = $instance->get($shipmentLabels);

        $this->assertInternalType('string', $result);
    }*/

    /**
     * @param array  $pdfFiles
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::pdfLabelFiles
     */
    public function testGetZendPdf($pdfFiles)
    {
        $shipmentLabels = [];

        foreach ($pdfFiles as $pdfLabel) {
            $shipmentLabels[] = $this->getShipmentLabelMock($pdfLabel);
        }

        $labelGeneratorMock = $this->getFakeMock(LabelGenerator::class);
        $labelGeneratorMock->setMethods(null);
        $labelGeneratorMock = $labelGeneratorMock->getMock();

        $instance = $this->getInstance(['labelGenerator' => $labelGeneratorMock]);
        $result = $this->invokeArgs('getZendPdf', [$shipmentLabels], $instance);

        $this->assertInternalType('string', $result);
    }

    public function testSaveTempLabel()
    {
        $ioFileMock = $this->getMockBuilder(File::class);
        $ioFileMock->setMethods(['checkAndCreateFolder', 'write']);
        $ioFileMock = $ioFileMock->getMock();

        $createFolderExpects = $ioFileMock->expects($this->once());
        $createFolderExpects->method('checkAndCreateFolder');

        $writeExpects = $ioFileMock->expects($this->once());
        $writeExpects->method('write');

        $instance = $this->getInstance(['ioFile' => $ioFileMock]);
        $result = $this->invokeArgs('saveTempLabel', ['Some file content'], $instance);

        $this->assertInternalType('string', $result);
        $this->assertContains(Generate::TEMP_LABEL_FOLDER, $result);
        $this->assertContains(Generate::TEMP_LABEL_FILENAME, $result);
    }

    /**
     * @param string $pdfLabel
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getShipmentLabelMock($pdfLabel)
    {
        $shipmentLabelMock = $this->getFakeMock(ShipmentLabel::class);
        $shipmentLabelMock->setMethods(['getLabel']);
        $shipmentLabelMock = $shipmentLabelMock->getMock();

        $getLabelExpects = $shipmentLabelMock->expects($this->once());
        $getLabelExpects->method('getLabel');
        $getLabelExpects->willReturn($pdfLabel);

        return $shipmentLabelMock;
    }
}
