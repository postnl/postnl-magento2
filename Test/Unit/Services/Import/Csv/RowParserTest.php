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
namespace TIG\PostNL\Unit\Services\Import\Csv;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Services\Import\Csv\RowParser;
use TIG\PostNL\Test\TestCase;

/**
 * Class CsvTest
 *
 * @package TIG\PostNL\Unit\Services\Import
 */
class RowParserTest extends TestCase
{
    protected $instanceClass = RowParser::class;

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
                'Please correct Table Rates format in the Row #9.'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $expected
     *
     * @throws LocalizedException
     * @throws \Exception
     *
     * @dataProvider getZipCodeProvider
     */
    public function testGetZipCode($rowData, $rowCount, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getZipcode', [$rowData, $rowCount], $instance);

            $this->assertEquals($expected, $result);
        } catch (LocalizedException $exception) {
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
                'Please correct Order Subtotal (and above) "non-integer" in the Row #2.',
            ],
            'invalid_column' => [
                [1 => 'incorrect column'],
                8,
                null,
                'Please correct Table Rates format in the Row #8.'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $conditionFullName
     * @param $expected
     *
     * @throws LocalizedException
     * @throws \Exception
     *
     * @dataProvider getConditionValueProvider
     */
    public function testGetConditionValue($rowData, $rowCount, $conditionFullName, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getConditionValue', [$rowData, $rowCount, $conditionFullName], $instance);

            $this->assertNotInternalType('string', $expected);
            $this->assertEquals($expected, $result);
        } catch (LocalizedException $exception) {
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
                'Please correct Shipping Price "non-integer value" in the Row #7.'
            ],
            'invalid_column' => [
                [5 => 'non-integer value'],
                4,
                'Please correct Table Rates format in the Row #4.'
            ]
        ];
    }

    /**
     * @param $rowData
     * @param $rowCount
     * @param $expected
     *
     * @throws LocalizedException
     * @throws \Exception
     *
     * @dataProvider getPriceProvider
     */
    public function testGetPrice($rowData, $rowCount, $expected)
    {
        $instance = $this->getInstance();

        try {
            $result = $this->invokeArgs('getPrice', [$rowData, $rowCount], $instance);

            $this->assertNotInternalType('string', $expected);
            $this->assertEquals($expected, $result);
        } catch (LocalizedException $exception) {
            $this->validateCaughtException($exception, $expected);
        }
    }

    /**
     * @param LocalizedException $exception
     * @param mixed              $expected
     *
     * @throws
     */
    private function validateCaughtException($exception, $expected)
    {
        if (!is_string($expected)) {
            throw $exception;
        }

        $exceptionMessage = $exception->getMessage();

        $this->assertInternalType('string', $exceptionMessage);
        $this->assertEquals($expected, $exceptionMessage);
    }
}
