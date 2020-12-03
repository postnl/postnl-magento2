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

    protected $groups = [
        'standard_options'              => 'Domestic options',
        'pakjegemak_options'            => 'Post Office options',
        'pakjegemak_be_options'         => 'Post Office Belgium options',
        'eu_options'                    => 'EU options',
        'global_options'                => 'Global options',
        'buspakje_options'              => 'Letter Box Parcel options',
        'extra_at_home_options'         => 'Extra@Home options',
        'id_check_options'              => 'ID Check options',
        'id_check_pakjegemak_options'   => 'ID Check Post Office options',
        'cargo_options'                 => 'Cargo options',
        'eps_package_options'           => 'Package options'
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
            [['isSunday' => true], ['3087', '3094', '3089', '3096', '3385', '3437', '3438', '3443', '3446', '3449',]],
            [['isEvening' => true],
             ['3087', '3094', '3089', '3096', '3090', '3385', '4938', '4941', '3437', '3438', '3443', '3446', '3449',]],
            [['isExtraCover' => true], ['3087', '3094', '3534', '3544', '3443', '3446', '3581', '3584',]],
            [['isGuaranteedDelivery' => true], ['3085', '3087', '3089', '3090', '3094', '3096',
                                                '3189', '3385', '3389', '3606', '3607',
                                                '3608', '3609', '3610', '3630', '3657']],
        ];
    }

    /**
     * @param $filter
     * @param $expected
     *
     * @throws \Exception
     * @dataProvider getProductoptionsProvider
     */
    public function testGetProductoptions($filter, $expected)
    {
        $instance = $this->getInstance();

        $options = $instance->get();
        $this->setProperty('availableOptions', $options, $instance);

        $result = $instance->getProductoptions($filter);

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
            [$this->groups, ['Domestic options',
                             'Post Office options',
                             'Post Office Belgium options',
                             'EU options',
                             'Global options',
                             'Letter Box Parcel options',
                             'Extra@Home options',
                             'ID Check options',
                             'ID Check Post Office options',
                             'Cargo options',
                             'Package options']]
        ];
    }

    /**
     * @param $groups
     * @param $expected
     *
     * @throws \Exception
     * @dataProvider setGroupedOptionsProvider
     */
    public function testGetGroupedOptions($groups, $expected)
    {
        $instance = $this->getInstance();
        $options = $instance->get();
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

    /**
     * @return array
     */
    public function getLabelProvider()
    {
        return [
            ['3085', 'Daytime', ['label' => 'Domestic', 'type' => '', 'comment' => 'Standard shipment']],
            ['3089', 'Daytime', ['label' => 'Delivery to stated address only', 'type' => '', 'comment' => 'Signature on delivery + Delivery to stated address only']],
            ['3089', 'Evening', ['label' => 'Delivery to stated address only', 'type' => 'Evening', 'comment' => 'Signature on delivery + Delivery to stated address only']],
            ['3534', 'PG',      ['label' => 'Post Office', 'type' => '', 'comment' => 'Post Office + Extra Cover']],
            ['4952', 'Daytime', ['label' => 'EPS', 'type' => '', 'comment' => 'EU Pack Special Consumer']],
            ['4945', 'Daytime', ['label' => 'Global Pack', 'type' => '', 'comment' => 'GlobalPack']],
            ['2928', 'Daytime', ['label' => 'Letter Box', 'type' => '', 'comment' => 'Letter Box Parcel Extra']],
            ['4944', 'Daytime', ['label' => 'EPS', 'type' => '', 'comment' => 'EU Pack Special Consumer']],
            ['1234', 'Daytime', ['label' => '', 'type' => '', 'comment' => '']],
        ];
    }

    /**
     * @param $code
     * @param $type
     * @param $expected
     *
     * @throws \Exception
     * @dataProvider getLabelProvider
     */
    public function testLabel($code, $type, $expected)
    {
        $instance = $this->getInstance();

        $this->assertEquals($expected, $instance->getLabel($code, $type));
    }

    public function testReturnsNullWhenCodeDoesNotExists()
    {
        $instance = $this->getInstance();

        $options = $instance->getOptionsByCode(-999999999);

        $this->assertNull($options);
    }
}
