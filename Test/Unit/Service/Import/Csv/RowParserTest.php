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
namespace TIG\PostNL\Unit\Service\Import\Csv;

use TIG\PostNL\Exception;
use TIG\PostNL\Service\Import\Csv\RowParser;
use TIG\PostNL\Test\TestCase;

class RowParserTest extends TestCase
{
    protected $instanceClass = RowParser::class;

    /**
     * @return array
     */
    public function parseRowProvider()
    {
        return [
            'valid_data ' => [
                [0 => '*', 1 => '*', 2 => '1234 AB', 3 => 56.78, 4 => 90.12],
                3,
                4,
                'package_value',
                null
            ],
            'incomplete_data ' => [
                [0 => '*', 1 => '*', 2 => '5678 CD'],
                9,
                0,
                'package_value',
                '[POSTNL-0247] Invalid PostNL Table Rates File Format in Row #9'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $websiteId
     * @param $conditionName
     * @param $expectedException
     *
     * @dataProvider parseRowProvider
     */
    public function testParseRow($rowData, $rowCount, $websiteId, $conditionName, $expectedException)
    {
        $instance = $this->getInstance();

        try {
            $result = $instance->parseRow($rowData, $rowCount, $websiteId, $conditionName, 'full_condition_name');

            $this->assertCount(7, $result);

            $this->assertEquals($websiteId, $result['website_id']);
            $this->assertEquals($conditionName, $result['condition_name']);

            $this->assertIsFloat($result['condition_value']);
            $this->assertIsFloat($result['price']);

            $this->assertArrayHasKey('dest_country_id', $result);
            $this->assertArrayHasKey('dest_region_id', $result);
            $this->assertArrayHasKey('dest_zip', $result);

            $this->assertNull($expectedException);
        } catch (Exception $exception) {
            $this->validateCaughtException($exception, $expectedException);
        }
    }

    /**
     * @return array
     */
    public function getCountryIdProvider()
    {
        return [
            'wildcard_country' => [
                [0 => '*'],
                1,
                '0',
            ],
            'invalid_column' => [
                [3 => 'incorrect column'],
                4,
                '[POSTNL-0247] Invalid PostNL Table Rates File Format in Row #4'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $expected
     *
     * @throws Exception
     *
     * @dataProvider getCountryIdProvider
     */
    public function testGetCountryId($rowData, $rowCount, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getCountryId', [$rowData, $rowCount], $instance);

            $this->assertEquals($expected, $result);
        } catch (Exception $exception) {
            $this->validateCaughtException($exception, $expected);
        }
    }

    /**
     * @return array
     */
    public function getRegionIdProvider()
    {
        return [
            'wildcard_by_region' => [
                [1 => '*'],
                2,
                3,
                '0',
            ],
            'wildcard_by_country' => [
                [1 => 'ID'],
                0,
                4,
                '0',
            ],
            'invalid_column' => [
                [5 => 'incorrect column'],
                6,
                7,
                '[POSTNL-0247] Invalid PostNL Table Rates File Format in Row #7'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $countryId
     * @param $rowCount
     * @param $expected
     *
     * @throws Exception
     *
     * @dataProvider getRegionIdProvider
     */
    public function testGetRegionId($rowData, $countryId, $rowCount, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getRegionId', [$rowData, $countryId, $rowCount], $instance);

            $this->assertEquals($expected, $result);
        } catch (Exception $exception) {
            $this->validateCaughtException($exception, $expected);
        }
    }

    /**
     * @return array
     */
    public function getZipCodeProvider()
    {
        return [
            'valid_zipcode' => [
                [2 => '1234 AB'],
                5,
                '1234 AB'
            ],
            'wildcard_zipcode' => [
                [2 => ''],
                6,
                '*',
            ],
            'invalid_column' => [
                [3 => 'incorrect column'],
                9,
                '[POSTNL-0247] Invalid PostNL Table Rates File Format in Row #9'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $expected
     *
     * @throws Exception
     *
     * @dataProvider getZipCodeProvider
     */
    public function testGetZipCode($rowData, $rowCount, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getZipcode', [$rowData, $rowCount], $instance);

            $this->assertEquals($expected, $result);
        } catch (Exception $exception) {
            $this->validateCaughtException($exception, $expected);
        }
    }

    /**
     * @return array
     */
    public function getConditionValueProvider()
    {
        return [
            'valid_condition_value' => [
                [3 => 678.95],
                4,
                null,
                678.95
            ],
            'invalid_condition_value' => [
                [3 => 'non-integer'],
                2,
                'Order Subtotal (and above)',
                '[POSTNL-0248] Invalid Order Subtotal (and above) "non-integer" supplied in row #2',
            ],
            'invalid_column' => [
                [1 => 'incorrect column'],
                8,
                null,
                '[POSTNL-0247] Invalid PostNL Table Rates File Format in Row #8'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $conditionFullName
     * @param $expected
     *
     * @throws Exception
     *
     * @dataProvider getConditionValueProvider
     */
    public function testGetConditionValue($rowData, $rowCount, $conditionFullName, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getConditionValue', [$rowData, $rowCount, $conditionFullName], $instance);

            $this->assertIsNotString($expected);
            $this->assertEquals($expected, $result);
        } catch (Exception $exception) {
            $this->validateCaughtException($exception, $expected);
        }
    }

    /**
     * @return array
     */
    public function getPriceProvider()
    {
        return [
            'valid_price' => [
                [4 => 123.45],
                6,
                123.45
            ],
            'invalid_price' => [
                [4 => 'non-integer value'],
                7,
                '[POSTNL-249] Invalid Shipping Price "non-integer value" supplied in row #7'
            ],
            'invalid_column' => [
                [5 => 'non-integer value'],
                4,
                '[POSTNL-0247] Invalid PostNL Table Rates File Format in Row #4'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $expected
     *
     * @throws Exception
     *
     * @dataProvider getPriceProvider
     */
    public function testGetPrice($rowData, $rowCount, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getPrice', [$rowData, $rowCount], $instance);

            $this->assertIsNotString($expected);
            $this->assertEquals($expected, $result);
        } catch (Exception $exception) {
            $this->validateCaughtException($exception, $expected);
        }
    }

    /**
     * @param Exception $exception
     * @param mixed     $expected
     *
     * @throws Exception
     */
    private function validateCaughtException($exception, $expected)
    {
        if (!is_string($expected)) {
            throw $exception;
        }

        $exceptionMessage = $exception->getMessage();

        $this->assertIsString($exceptionMessage);
        $this->assertEquals($expected, $exceptionMessage);
    }
}
