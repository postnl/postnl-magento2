<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment\Packingslip;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;
use TIG\PostNL\Service\Shipment\Packingslip\MergeWithLabels;
use TIG\PostNL\Test\TestCase;

class MergeWithLabelsTest extends TestCase
{
    public $instanceClass = MergeWithLabels::class;

    public function testSetY()
    {
        $instance = $this->getInstance();
        $instance->setY(4);

        $result = $this->getProperty('packingslipYPos', $instance);
        $this->assertEquals(4, $result);
    }

    /**
     * @return array
     */
    public function mergeTogetherProvider()
    {
        return [
            'no labels' => [
                [],
                'packingslip',
                0,
                'packingslip'
            ],
            'single label, low y position' => [
                ['label'],
                'packingslip',
                100,
                'merged packingslip'
            ],
            'single label, high y position' => [
                ['label'],
                'packingslip',
                500,
                'merged first label with packingslip'
            ],
            'mutliple label, low y position' => [
                ['label', 'another label'],
                'packingslip',
                200,
                'merged packingslip'
            ],
            'mutliple label, high y position' => [
                ['label', 'another label'],
                'packingslip',
                600,
                'merged packingslip'
            ],
        ];
    }

    /**
     * @param $labels
     * @param $packingslip
     * @param $yPosition
     * @param $expected
     *
     * @dataProvider mergeTogetherProvider
     */
    public function testMergeTogether($labels, $packingslip, $yPosition, $expected)
    {
        $labelModels = [];

        foreach ($labels as $label) {
            $mock = $this->getFakeMock(ShipmentLabelInterface::class)
                ->setMethods(['getLabel'])
                ->getMockForAbstractClass();
            $mock->method('getLabel')->willReturn($label);
            $labelModels[] = $mock;
        }

        $getLabelsMock = $this->getFakeMock(GetLabels::class)->setMethods(['get'])->getMock();
        $getLabelsMock->expects($this->once())->method('get')->willReturn($labelModels);

        $labelGenerateMock = $this->getFakeMock(LabelGenerate::class)->setMethods(['run'])->getMock();
        $labelGenerateMock->method('run')->willReturn('labelpdf');

        $packingslipGenerateMock = $this->getFakeMock(PackingslipGenerate::class)->setMethods(['run'])->getMock();
        $packingslipGenerateMock->method('run')->willReturn('merged packingslip');

        $fpdiMock = $this->getFakeMock(Fpdi::class)
            ->setMethods(['Output', 'addSinglePage', 'addMultiplePages', 'Rotate'])
            ->getMock();
        $fpdiMock->method('Output')->willReturn('merged first label with packingslip');

        $fpdiFactoryMock = $this->getFakeMock(FpdiFactory::class)->setMethods(['create', 'saveFile', 'cleanupFiles'])->getMock();
        $fpdiFactoryMock->method('create')->willReturn($fpdiMock);

        $instance = $this->getInstance([
            'getLabels' => $getLabelsMock,
            'labelGenerator' => $labelGenerateMock,
            'packingslipGenerator' => $packingslipGenerateMock,
            'fpdiFactory' => $fpdiFactoryMock
        ]);
        $instance->setY($yPosition);

        $result = $instance->merge(0, $packingslip, true);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function mergeSeparateProvider()
    {
        return [
            'with labels' => [
                ['label'],
                'packingslip',
                'merged packingslip'
            ],
            'no labels' => [
                [],
                'packingslip',
                'packingslip'
            ]
        ];
    }

    /**
     * @param $labels
     * @param $packingslip
     * @param $expected
     *
     * @dataProvider mergeSeparateProvider
     */
    public function testMergeSeparate($labels, $packingslip, $expected)
    {
        $getLabelsMock = $this->getFakeMock(GetLabels::class)->setMethods(['get'])->getMock();
        $getLabelsMock->expects($this->once())->method('get')->willReturn($labels);

        $labelGenerateMock = $this->getFakeMock(LabelGenerate::class)->setMethods(['run'])->getMock();
        $labelGenerateMock->method('run')->with($labels, true)->willReturn('labelpdf');

        $packingslipGenerateMock = $this->getFakeMock(PackingslipGenerate::class)->setMethods(['run'])->getMock();
        $packingslipGenerateMock->method('run')->with([$packingslip, 'labelpdf'])->willReturn('merged packingslip');

        $instance = $this->getInstance([
            'getLabels' => $getLabelsMock,
            'labelGenerator' => $labelGenerateMock,
            'packingslipGenerator' => $packingslipGenerateMock
        ]);
        $result = $instance->merge(0, $packingslip, false);
        $this->assertEquals($expected, $result);
    }

    public function testMergeFirstLabel()
    {
        $fpdiMock = $this->getFakeMock(Fpdi::class)
            ->setMethods(['addMultiplePages', 'addSinglePage', 'Rotate', 'pixelsToPoints', 'Output'])
            ->getMock();
        $fpdiMock->expects($this->once())->method('addMultiplePages')->with('packingslipfile', 0, 0);
        $fpdiMock->expects($this->once())->method('addSinglePage')->with('labelfile');
        $fpdiMock->expects($this->exactly(2))->method('Rotate')->withConsecutive([90], [0]);
        $fpdiMock->expects($this->exactly(3))->method('pixelsToPoints')->withConsecutive([-1037], [413], [538]);
        $fpdiMock->expects($this->once())->method('Output')->with('s')->willReturn('merged label');

        $fpdiFactoryMock = $this->getFakeMock(FpdiFactory::class)->setMethods(['create', 'saveFile', 'cleanupFiles'])->getMock();
        $fpdiFactoryMock->expects($this->once())->method('create')->willReturn($fpdiMock);
        $fpdiFactoryMock->expects($this->once())->method('cleanupFiles');
        $fpdiFactoryMock->expects($this->exactly(2))
            ->method('saveFile')
            ->withConsecutive(['packingslip'], ['label'])
            ->willReturnOnConsecutiveCalls('packingslipfile', 'labelfile');

        $instance = $this->getInstance(['fpdiFactory' => $fpdiFactoryMock]);
        $result = $this->invokeArgs('mergeFirstLabel', ['label', 'packingslip'], $instance);
        $this->assertEquals('merged label', $result);
    }
}
