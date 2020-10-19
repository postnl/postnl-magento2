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
namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Shipment\Label\Merge;
use TIG\PostNL\Test\TestCase;

class MergeTest extends TestCase
{
    public $instanceClass = Merge::class;

    public function theRightMergerIsCalledProvider()
    {
        return [
            ['A4'],
            ['A6'],
        ];
    }

    /**
     * @dataProvider theRightMergerIsCalledProvider
     *
     * @param $merger
     */
    public function testRightMergerIsCalled($merger)
    {
        $shipmentLabelDomesticMock = $this->getMock(ShipmentLabelInterface::class);
        $shipmentLabelDomesticMock->shipmentType = 'Domestic';
        $shipmentLabelGPMock = $this->getMock(ShipmentLabelInterface::class);
        $shipmentLabelGPMock->shipmentType = 'GP';

        $labels = [$shipmentLabelDomesticMock, $shipmentLabelGPMock];

        // A4 mode should print everything A4
        $a4Labels = [0 => $shipmentLabelDomesticMock, 1 => $shipmentLabelGPMock];
        $a6Labels = null;

        // A6 mode should print GP as A4, Domestic as A6
        if ($merger == 'A6') {
            $a4Labels = [1 => $shipmentLabelGPMock];
            $a6Labels = [0 => $shipmentLabelDomesticMock];
        }

        $webshopMock = $this->getFakeMock(\TIG\PostNL\Config\Provider\Webshop::class, true);
        $labelSize = $webshopMock->method('getLabelSize');
        $labelSize->willReturn($merger);

        $fpdiMock = $this->getMock(Fpdi::class);
        $concatPdfMock = $fpdiMock->method('concatPdf');
        $concatPdfMock->willReturn($fpdiMock);

        $a4Merger = $this->getFakeMock(Merge\A4Merger::class, true);
        // Expected once in A6 as well, as Globalpack will also be printed with A6 settings.
        $files = $a4Merger->expects($this->once());
        $files->method('files');
        $files->with($a4Labels);
        $files->willReturn($fpdiMock);

        $a6Merger = $this->getFakeMock(Merge\A6Merger::class, true);
        $files = $a6Merger->expects($merger == 'A6' ? $this->once() : $this->never());
        $files->method('files');
        $files->with($a6Labels);
        $files->willReturn($fpdiMock);

        /** @var Merge $instance */
        $instance = $this->getInstance([
            'a4Merger' => $a4Merger,
            'a6Merger' => $a6Merger,
            'webshopConfiguration' => $webshopMock,
        ]);

        $instance->files($labels);
    }
}
