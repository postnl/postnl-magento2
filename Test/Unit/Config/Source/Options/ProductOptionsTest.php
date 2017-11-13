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
namespace TIG\PostNL\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ProductOptionsTest extends TestCase
{
    protected $instanceClass = ProductOptions::class;

    protected $options = [
        '3385' => [
            'value'             => '3385',
            'label'             => 'label',
            'isEvening'         => true,
            'isSunday'          => true,
            'group'             => 'standard_options',
        ],
        // Pakjegemak Options
        '3534' => [
            'value'             => '3534',
            'label'             => 'label',
            'isExtraEarly'      => false,
            'isSunday'          => false,
            'group'             => 'pakjegemak_options',
        ],
        '3544' => [
            'value'             => '3544',
            'label'             => 'label',
            'isExtraCover'      => true,
            'isExtraEarly'      => true,
            'group'             => 'pakjegemak_options',
        ],
        '3089' => [
            'value'             => '3089',
            'label'             => 'label',
            'isEvening'         => true,
            'isSunday'          => true,
            'group'             => 'standard_options',
        ],
    ];

    protected $groups = [
        'standard_options'   => 'Domestic options',
        'pakjegemak_options' => 'Post Office options',
    ];

    /**
     * Test option Array to not be empty
     */
    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertNotEmpty($options);
    }

    /**
     * @return array
     */
    public function getProductoptionsProvider()
    {
        return [
            [$this->options, ['isEvening' => true], false, ['3385', '3089']],
            [$this->options, ['isSunday'  => false], false, ['3534']],
            [$this->options, ['isEvening' => true], true, ['3089']]
        ];
    }

    /**
     * @param $options
     * @param $filter
     * @param $checkOnAvailable
     * @param $expected
     *
     * @dataProvider getProductoptionsProvider
     */
    public function testGetProductoptions($options, $filter, $checkOnAvailable, $expected)
    {
        $instance = $this->getInstance();

        $this->setProperty('availableOptions', $options, $instance);

        $result = $instance->getProductoptions($filter, $checkOnAvailable);

        $result = array_map(function ($value) {
            return $value['value'];
        }, $result);

        foreach ($expected as $productCode) {
            $this->assertTrue(in_array($productCode, $result));
        }
    }

    /**
     * @return array
     */
    public function setGroupedOptionsProvider()
    {
        return [
            [$this->options, $this->groups, ['Domestic options', 'Post Office options']]
        ];
    }

    /**
     * @param $options
     * @param $groups
     * @param $expected
     *
     * @dataProvider setGroupedOptionsProvider
     */
    public function testGetGroupedOptions($options, $groups, $expected)
    {
        $instance = $this->getInstance();
        $instance->setGroupedOptions($options, $groups);

        $result = $instance->getGroupedOptions();

        $result = array_map(function ($value) {
            return $value['label'];
        }, $result);

        $countResult   = count($result);
        $countExpected = count($expected);

        $this->assertTrue($countResult == $countExpected);

        foreach ($result as $group) {
            $this->assertTrue(in_array($group, $expected));
        }
    }

    /**
     * @return array
     */
    public function getExtraAtHomeOptionsProvider()
    {
        return [
            'no options configured' => [
                false,
                8,
                [3628,3629,3653,3783,3790,3791,3792,3793]
            ],
            'single E@H option configured' => [
                '3628',
                1,
                [3628]
            ],
            'single non-E@H option configured' => [
                '3085',
                1,
                [0]
            ],
            'multiple E@H options configured' => [
                '3629,3653',
                2,
                [3629,3653]
            ],
            'multiple non-E@H options configured' => [
                '3189,3089',
                1,
                [0]
            ],
            'multiple mixed options configured' => [
                '3389,3653,3096,3090,3783,3793',
                3,
                [3653,3783,3793]
            ],
        ];
    }

    /**
     * @param $productOptions
     * @param $expectedCount
     * @param $expectedValues
     *
     * @dataProvider getExtraAtHomeOptionsProvider
     */
    public function testGetExtraAtHomeOptions($productOptions, $expectedCount, $expectedValues)
    {
        $configMock = $this->getFakeMock(\TIG\PostNL\Config\Provider\ProductOptions::class)
            ->setMethods(['getSupportedProductOptions'])
            ->getMock();
        $configMock->expects($this->atLeastOnce())->method('getSupportedProductOptions')->willReturn($productOptions);

        $instance = $this->getInstance(['config' => $configMock]);
        $result = $instance->getExtraAtHomeOptions();

        $this->assertCount($expectedCount, $result);
        $resultValues = [];

        foreach ($result as $option) {
            $resultValues[] = $option['value'];
            $this->assertInstanceOf(\Magento\Framework\Phrase::class, $option['label']);
        }

        $this->assertEquals($resultValues, $expectedValues, '', 0.0, 10, true);
    }

    public function testReturnsTheCorrectCode()
    {
        $instance = $this->getInstance();

        $options = $instance->getOptionsByCode(3085);

        $this->assertEquals(3085, $options['value']);
        $this->assertEquals('Standard shipment', $options['label']);
        $this->assertEquals(false, $options['isExtraCover']);
        $this->assertEquals(false, $options['isEvening']);
        $this->assertEquals(false, $options['isSunday']);
        $this->assertEquals('NL', $options['countryLimitation']);
        $this->assertEquals('standard_options', $options['group']);
    }

    public function testReturnsNullWhenCodeDoesNotExists()
    {
        $instance = $this->getInstance();

        $options = $instance->getOptionsByCode(-999999999);

        $this->assertNull($options);
    }
}
