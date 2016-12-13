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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Test\TestCase;

/**
 * Class ProductOptionsTest
 *
 * @package TIG\PostNL\Unit\Config\Source
 */
class ProductOptionsTest extends TestCase
{
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
    ];

    protected $groups = [
        'standard_options'   => 'Domestic options',
        'pakjegemak_options' => 'Post Office options',
    ];

    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance($args = [])
    {
        return $this->objectManager->getObject(ProductOptions::class, $args);
    }

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
            [$this->options, ['isEvening' => true], ['3385']],
            [$this->options, ['isSunday' => false], ['3534']],
            [$this->options, ['isSunday' => true], ['3385']]
        ];
    }

    /**
     * @param $options
     * @param $filter
     * @param $expected
     *
     * @dataProvider getProductoptionsProvider
     */
    public function testGetProductoptions($options, $filter, $expected)
    {
        $instance = $this->getInstance();

        $this->setProperty('availableOptions', $options, $instance);

        $result = $instance->getProductoptions($filter);

        $result = array_map( function ($value) {
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

        $result = array_map( function ($value) {
            return $value['label'];
        }, $result);

        $countResult   = count($result);
        $countExpected = count($expected);

        $this->assertTrue($countResult == $countExpected);

        foreach ($result as $group) {
            $this->assertTrue(in_array($group, $expected));
        }
    }

}