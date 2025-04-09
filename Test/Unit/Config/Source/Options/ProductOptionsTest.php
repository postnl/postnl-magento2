<?php

namespace TIG\PostNL\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ProductOptionsTest extends TestCase
{
    protected $instanceClass = ProductOptions::class;

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
            [['isEvening' => true],
             ['3087', '3094', '3089', '3096', '3090', '3385', '4941', '3437', '3438', '3443', '3446', '3449',]],
            [['isExtraCover' => true], ['3087', '3094', '3534', '3544', '3443', '3446', '3581', '3584',]],
            [['isGuaranteedDelivery' => true], ['3085', '3087', '3089', '3090', '3094', '3096',
                                                '3189', '3385', '3389']],
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

    public function testReturnsTheCorrectCode()
    {
        $instance = $this->getInstance();

        $options = $instance->getOptionsByCode(3085);

        $this->assertEquals(3085, $options['value']);
        $this->assertEquals('Standard shipment', $options['label']);
        $this->assertEquals(false, $options['isExtraCover']);
        $this->assertEquals(true, $options['isEvening']);
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
            ['2928', 'Daytime', ['label' => 'Letter Box', 'type' => '', 'comment' => 'Letter Box Parcel Extra']],
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
