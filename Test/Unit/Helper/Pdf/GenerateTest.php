<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Helper\Pdf;

use Magento\Framework\Filesystem\Io\File;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use TIG\PostNL\Helper\Pdf\Generate;
use TIG\PostNL\Model\ShipmentLabel;
use TIG\PostNL\Test\TestCase;

class GenerateTest extends TestCase
{
    protected $instanceClass = Generate::class;

    /**
     * @param array  $pdfs
     * @param string $mergedPdf
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::pdfLabelsEncoded
     */
    public function testGetZendPdf($pdfs, $mergedPdf)
    {
        $shipmentLabels = [];

        foreach ($pdfs as $pdfLabel) {
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
