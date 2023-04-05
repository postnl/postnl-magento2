<?php

namespace PostNL\Test\Unit\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Shipment\Label\Type\EPS;

class EPSTest extends TestCase
{
    /** @var EPS $instanceClass */
    public $instanceClass = EPS::class;

    /**
     * @throws \Exception
     */
    public function testIsPriorityProduct()
    {
        /** @var EPS $instance */
        $instance = $this->getInstance();
        $result   = $instance->isPriorityProduct(6350);
        $expected = true;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws \Exception
     */
    public function testIsRotatedProduct()
    {
        /** @var EPS $instance */
        $instance = $this->getInstance();
        $result   = $instance->isRotatedProduct(4940);
        $expected = true;

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function rotateReturnProductProvider()
    {
        return [
            'ProductCode should rotate, is return label' => [4946, true, true],
            'ProductCode should rotate, is not return label' => [4946, false, false],
            'ProductCode should not rotate, is return label' => [4950, true, false],
            'ProductCode should not rotate, is not return label' => [5060, false, false]
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

        $instance = $this->getInstance();
        $result = $this->invokeArgs('rotateReturnProduct', [$labelMock], $instance);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function shouldRotateProvider()
    {
        return [
            'is rotated product code, not return label' => [4950, false, true],
            'is rotated product code, is return label' => [3622, true, true],
            'is priority product code, not return label' => [6942, false, false],
            'is priority product code, is return label' => [6350, true, false],
            'is return product code, not return label' => [4946, false, false],
            'is return product code, is return label' => [4946, true, true],
            'is other product code, not return label' => [3385, false, false],
            'is other product code, is return label' => [3790, true, false]
        ];
    }

    /**
     * @param $productCode
     * @param $isReturnLabel
     * @param $expected
     *
     * @throws \Exception
     *
     * @dataProvider shouldRotateProvider
     */
    public function testShouldRotate($productCode, $isReturnLabel, $expected)
    {
        $labelMock = $this->getFakeMock(ShipmentLabelInterface::class)
            ->setMethods(['getProductCode', 'getReturnLabel'])
            ->getMockForAbstractClass();
        $labelMock->method('getProductCode')->willReturn($productCode);
        $labelMock->method('getReturnLabel')->willReturn($isReturnLabel);

        $fpdiMock = $this->getFakeMock(Fpdi::class)->setMethods(['importPage', 'getTemplateSize'])->getMock();
        $fpdiMock->expects($this->once())->method('importPage')->willReturn(1);
        $fpdiMock->expects($this->once())->method('getTemplateSize')->with(1)->willReturn([]);

        $instance = $this->getInstance(['pdf' => $fpdiMock]);
        $result = $this->invokeArgs('shouldRotate', [$labelMock], $instance);
        $this->assertEquals($expected, $result);
    }
}
