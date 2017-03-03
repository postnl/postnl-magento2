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

use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\Pdf\Fpdf;
use TIG\PostNL\Helper\Pdf\Positions;
use TIG\PostNL\Test\TestCase;

class FpdfTest extends TestCase
{
    protected $instanceClass = Fpdf::class;

    /**
     * @param $pdfPaths
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::pdfLabelPaths
     */
    public function testAddLabel($pdfPaths)
    {
        $expectedCount = count($pdfPaths);

        $positionsMock = $this->getFakeMock(Positions::class);
        $positionsMock->setMethods(['getForPosition']);
        $positionsMock = $positionsMock->getMock();

        $getForPositionExpects = $positionsMock->expects($this->exactly($expectedCount));
        $getForPositionExpects->method('getForPosition');

        $instance = $this->getInstance(['positions' => $positionsMock]);

        foreach ($pdfPaths as $pdf) {
            $instance->addLabel($pdf, 'label');
        }

        $result = $instance->getLabelCounter();
        $this->assertEquals($expectedCount, $result);
    }

    /**
     * @return array
     */
    public function updatePageProvider()
    {
        return [
            ['A4', 2, 3],
            ['A4', Fpdf::MAX_LABELS_PER_PAGE, 1],
            ['A6', 1, 3]
        ];
    }

    /**
     * @param $labelSize
     * @param $currentLabelCount
     * @param $expectedLabelCount
     *
     * @dataProvider updatePageProvider
     */
    public function testUpdatePage($labelSize, $currentLabelCount, $expectedLabelCount)
    {
        $webshopMock = $this->getFakeMock(Webshop::class);
        $webshopMock->setMethods(['getLabelSize']);
        $webshopMock = $webshopMock->getMock();

        $getLabelSizeExpects = $webshopMock->expects($this->once());
        $getLabelSizeExpects->method('getLabelSize');
        $getLabelSizeExpects->willReturn($labelSize);

        $instance = $this->getInstance(['webshop' => $webshopMock]);
        $instance->setLabelCounter($currentLabelCount);
        $instance->updatePage();

        $result = $instance->getLabelCounter();
        $this->assertEquals($expectedLabelCount, $result);
    }

    public function testSetLabelCounter()
    {
        $randomLabelCounter = rand(0, 10);

        $instance = $this->getInstance();
        $instance->setLabelCounter($randomLabelCounter);
        $result = $instance->getLabelCounter();

        $this->assertEquals($randomLabelCounter, $result);
    }

    public function testIncreaseLabelCounter()
    {
        $randomLabelCounter = rand(0, 10);
        $expected = ($randomLabelCounter + 1);

        $instance = $this->getInstance();
        $instance->setLabelCounter($randomLabelCounter);
        $instance->increaseLabelCounter();
        $result = $instance->getLabelCounter();

        $this->assertEquals($expected, $result);
    }

    public function testResetLabelCounter()
    {
        $instance = $this->getInstance();
        $instance->resetLabelCounter();
        $result = $instance->getLabelCounter();

        $this->assertEquals(1, $result);
    }
}
