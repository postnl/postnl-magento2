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
namespace TIG\PostNL\Test\Unit\Service\Order;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Order\FeeCalculator;
use TIG\PostNL\Test\TestCase;

class FeeCalculatorTest extends TestCase
{
    public $instanceClass = FeeCalculator::class;

    public function feeCalculationProvider()
    {
        return [
            'Evening delivery with fee' => [
                'params' => ['option' => 'Evening'],
                'config options' => [
                    'isEveningDeliveryActive' => true,
                    'getEveningDeliveryFee' => 3,
                ],
                'expected' => 3.0
            ],
            'Evening delivery with fee but inactive' => [
                'params' => ['option' => 'Evening'],
                'config options' => [
                    'isEveningDeliveryActive' => false,
                    'getEveningDeliveryFee' => 3,
                ],
                'expected' => 0.0
            ],
            'Evening delivery without fee' => [
                'params' => ['option' => 'Evening'],
                'config options' => [
                    'isEveningDeliveryActive' => true,
                    'getEveningDeliveryFee' => '',
                ],
                'expected' => 0.0
            ],
            'Sunday delivery with fee' => [
                'params' => ['option' => 'Sunday'],
                'config options' => [
                    'isSundayDeliveryActive' => true,
                    'getSundayDeliveryFee' => 3,
                ],
                'expected' => 3.0
            ],
            'Sunday delivery with fee but inactive' => [
                'params' => ['option' => 'Sunday'],
                'config options' => [
                    'isSundayDeliveryActive' => false,
                    'getSundayDeliveryFee' => 3,
                ],
                'expected' => 0.0
            ],
            'Sunday delivery without fee' => [
                'params' => ['option' => 'Sunday'],
                'config options' => [
                    'isSundayDeliveryActive' => true,
                    'getSundayDeliveryFee' => '',
                ],
                'expected' => 0.0
            ],
            'params are missing' => [
                'params' => [],
                'config options' => [
                    'isSundayDeliveryActive' => true,
                    'getSundayDeliveryFee' => 3,
                    'isEveningDeliveryActive' => true,
                    'getEveningDeliveryFee' => 3,
                ],
                'expected' => 0.0
            ],
        ];
    }

    /**
     * @dataProvider feeCalculationProvider
     */
    public function testFeeCalculation($params, $configOptions, $expected)
    {
        $configMock = $this->getFakeMock(ShippingOptions::class)->getMock();
        $this->addConfigOptions($configOptions, $configMock);

        /** @var FeeCalculator $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $configMock
        ]);

        $result = $instance->get($params);
        $this->assertEquals($expected, $result);
        $this->assertSame($expected, $result);
    }

    private function addConfigOptions($configOptions, \PHPUnit\Framework\MockObject\MockObject $instance)
    {
        foreach ($configOptions as $method => $value) {
            $expected = $instance->method($method);
            $expected->willReturn($value);
        }
    }
}
