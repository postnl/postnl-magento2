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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace PostNL\Test\Unit\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Config\Source\Options\DefaultOptions;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Shipment\Label\Type\Domestic;

class DomesticTest extends TestCase
{
    /** @var Domestic $instanceClass */
    public $instanceClass = Domestic::class;

    /**
     * @return array
     */
    public function rotateReturnProductProvider()
    {
        return [
            'ProductCode should rotate, is return label' => [4946, true, true],
            'ProductCode should rotate, is not return label' => [4946, false, false],
            'ProductCode should not rotate, is return label' => [3085, true, false],
            'ProductCode should not rotate, is not return label' => [3085, false, false]
        ];
    }

    /**
     * @param $productCode
     * @param $isReturnLabel
     * @param $expected
     *
     * @throws \Exception
     *
     * @dataProvider rotateReturnProductProvider
     */
    public function testRotateReturnProduct($productCode, $isReturnLabel, $expected)
    {
        $labelMock = $this->getFakeMock(ShipmentLabelInterface::class)
            ->setMethods(['getProductCode', 'getReturnLabel'])
            ->getMockForAbstractClass();
        $labelMock->expects($this->once())->method('getProductCode')->willReturn($productCode);
        $labelMock->method('getReturnLabel')->willReturn($isReturnLabel);

        $productOptionsMock = $this->getFakeMock(DefaultOptions::class)->getMock();
        $productOptionsExpects = $productOptionsMock->expects($this->once());
        $productOptionsExpects->method('getBeProducts');
        $productOptionsExpects->willReturn([['value' => 4946]]);

        $productDomesticOptionsExpects = $productOptionsMock->expects($this->once());
        $productDomesticOptionsExpects->method('getBeDomesticProducts');
        $productDomesticOptionsExpects->willReturn([]);

        $instance = $this->getInstance(['defaultOptions' => $productOptionsMock]);
        $result = $this->invokeArgs('rotateReturnProduct', [$labelMock], $instance);
        $this->assertEquals($expected, $result);
    }
}
