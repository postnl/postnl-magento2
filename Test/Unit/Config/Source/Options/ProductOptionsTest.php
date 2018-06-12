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

use Magento\Framework\Phrase;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsProvider;
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
        '3087' => [
            'value'             => '3087',
            'label'             => 'label',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'group'             => 'standard_options',
        ],
        '3094' => [
            'value'             => '3094',
            'label'             => 'label',
            'isExtraCover'      => true,
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
        //ID Check
        '3437' => [
            'value'             => '3437',
            'label'             => 'label',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3438' => [
            'value'             => '3438',
            'label'             => 'label',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3443' => [
            'value'             => '3443',
            'label'             => 'label',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3446' => [
            'value'             => '3446',
            'label'             => 'label',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3449' => [
            'value'             => '3449',
            'label'             => 'label',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        // ID Check Pakje gemak
        '3571' => [
            'value'             => '3571',
            'label'             => 'label',
            'isExtraCover'      => false,
            'pge'               => false,
            'statedAddressOnly' => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ],
        '3574' => [
            'value'             => '3574',
            'label'             => 'label',
            'isExtraCover'      => false,
            'pge'               => true,
            'statedAddressOnly' => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ],
        '3581' => [
            'value'             => '3581',
            'label'             => 'label',
            'isExtraCover'      => true,
            'pge'               => false,
            'statedAddressOnly' => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ],
        '3584' => [
            'value'             => '3584',
            'label'             => 'label',
            'isExtraCover'      => true,
            'pge'               => true,
            'statedAddressOnly' => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ]
    ];

    protected $groups = [
        'standard_options'   => 'Domestic options',
        'pakjegemak_options' => 'Post Office options',
        'id_check_options'   => 'ID Check options',
        'id_check_pakjegemak_options' => 'ID Check Post Office options',
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
            [$this->options, ['isEvening' => true], false, ['3385', '3089', '3437', '3438', '3443', '3446', '3449']],
            [$this->options, ['isSunday'  => false], false, ['3534', '3571', '3574', '3581', '3574']],
            [$this->options, ['isEvening' => true], true, ['3089', '3437', '3438', '3443', '3446', '3449']],
            [$this->options, ['isExtraCover' => true], true, ['3087', '3094', '3443', '3446', '3581', '3584']]
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
            [$this->options, $this->groups, ['Domestic options',
                                             'Post Office options',
                                             'ID Check options',
                                             'ID Check Post Office options']]
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
    public function getExtraCoverProductOptionsProvider()
    {
        return [
            'no options configured' => [
                false,
                8,
                [3087,3094,3534,3544,3443,3446,3581,3584]
            ],
            'single Extra Cover option configured' => [
                '3087',
                1,
                [3087]
            ],
            'single non Extra Cover option configured' => [
                '3085',
                1,
                [0]
            ],
            'multiple E@H options configured' => [
                '3087,3534',
                2,
                [3087,3534]
            ],
            'multiple non Extra Cover options configured' => [
                '3189,3089',
                1,
                [0]
            ],
            'multiple mixed options configured' => [
                '3389,3087,3096,3090,3094,3544',
                3,
                [3087,3094,3544]
            ],
        ];
    }

    /**
     * @param $productOptions
     * @param $expectedCount
     * @param $expectedValues
     *
     * @dataProvider getExtraCoverProductOptionsProvider
     */
    public function testGetExtraCoverProductOptions($productOptions, $expectedCount, $expectedValues)
    {
        $configMock = $this->getFakeMock(ProductOptionsProvider::class)
            ->setMethods(['getSupportedProductOptions'])
            ->getMock();
        $configMock->expects($this->atLeastOnce())->method('getSupportedProductOptions')->willReturn($productOptions);

        $instance = $this->getInstance(['config' => $configMock]);
        $result = $instance->getExtraCoverProductOptions();

        $this->assertCount($expectedCount, $result);
        $resultValues = [];

        foreach ($result as $option) {
            $resultValues[] = $option['value'];
            $this->assertInstanceOf(Phrase::class, $option['label']);
        }

        $this->assertEquals($resultValues, $expectedValues, '', 0.0, 10, true);
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
        $configMock = $this->getFakeMock(ProductOptionsProvider::class)
            ->setMethods(['getSupportedProductOptions'])
            ->getMock();
        $configMock->expects($this->atLeastOnce())->method('getSupportedProductOptions')->willReturn($productOptions);

        $instance = $this->getInstance(['config' => $configMock]);
        $result = $instance->getExtraAtHomeOptions();

        $this->assertCount($expectedCount, $result);
        $resultValues = [];

        foreach ($result as $option) {
            $resultValues[] = $option['value'];
            $this->assertInstanceOf(Phrase::class, $option['label']);
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
