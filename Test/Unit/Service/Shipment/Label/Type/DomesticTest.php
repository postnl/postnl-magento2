<?php

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
