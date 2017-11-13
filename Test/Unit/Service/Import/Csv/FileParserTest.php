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

use Magento\Framework\Filesystem\File\ReadInterface;
use TIG\PostNL\Exception;
use TIG\PostNL\Service\Import\ParseErrors;
use TIG\PostNL\Service\Import\Csv\FileParser;
use TIG\PostNL\Service\Import\Csv\RowParser;
use TIG\PostNL\Test\TestCase;

class FileParserTest extends TestCase
{
    protected $instanceClass = FileParser::class;

    /**
     * @return array
     */
    public function hasErrorsProvider()
    {
        return [
            'no_errors' => [
                [],
                false
            ],
            'has_errors' => [
                ['Column "price" not found', 'Row #2 invalid'],
                true
            ]
        ];
    }

    /**
     * @param $errors
     * @param $expected
     *
     * @dataProvider hasErrorsProvider
     */
    public function testHasErrors($errors, $expected)
    {
        /** @var ParseErrors $parserError */
        $parserErrors = $this->getObject(ParseErrors::class);

        foreach ($errors as $error) {
            $parserErrors->addError($error);
        }

        $instance = $this->getInstance(['parserErrors' => $parserErrors]);
        $result = $instance->hasErrors();

        $this->assertInternalType('bool', $result);
        $this->assertEquals($expected, $result);
    }

    public function testGetErrors()
    {
        /** @var ParseErrors $parserError */
        $parserErrors = $this->getObject(ParseErrors::class);
        $parserErrors->addError('Column "price" not found');
        $parserErrors->addError('Row #2 invalid');

        $instance = $this->getInstance(['parserErrors' => $parserErrors]);

        $result = $instance->getErrors();

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
    }

    public function testGetColumns()
    {
        $instance = $this->getInstance();
        $result = $instance->getColumns();

        $this->assertInternalType('array', $result);
        $this->assertCount(7, $result);
    }

    /**
     * @return array
     */
    public function getRowsProvider()
    {
        return [
            'valid_data' => [
                [
                    ['Country', 'Region', 'Zipcode', 'Condition_value', 'Price'],
                    ['*', '*', '*', '1.23', '2.34'],
                    ['*', '*', '1234 AB', '3.45', '4.56']
                ],
                0,
                2
            ],
            'invalid_data_by_rows' => [
                [
                    ['Country', 'Region', 'Zipcode', 'Condition_value', 'Price'],
                    ['*', '*', '*', 'non-integer', '7.89'],
                    ['*', '*', '2345 CD', '0.12', '12.23'],
                    ['*', '*', '3456 EF', '23.45']
                ],
                2,
                1
            ],
            'invalid_data_by_header' => [
                [
                    ['Country', 'Region', 'Zipcode', 'Condition_value'],
                    ['*', '*', '*', '45.67', '56.78']
                ],
                1,
                1
            ]
        ];
    }

    /**
     * @param $csvRows
     * @param $expectedErrors
     * @param $expectedValidRows
     *
     * @dataProvider getRowsProvider
     */
    public function testGetRows($csvRows, $expectedErrors, $expectedValidRows)
    {
        $rowParser = $this->getObject(RowParser::class);
        $parserErrors = $this->getObject(ParseErrors::class);
        $csvRows[] = false;

        $readInterfaceMock = $this->getFakeMock(ReadInterface::class)->getMock();
        $readInterfaceMock->expects($this->atLeastOnce())->method('readCsv')->will(
            new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($csvRows)
        );

        $instance = $this->getInstance(['rowParser' => $rowParser, 'parserErrors' => $parserErrors]);
        $result = $instance->getRows($readInterfaceMock, rand(0, 10), 'conditionName', 'fullConditionName');

        $validRows = 0;

        foreach ($result as $index => $resultGroup) {
            foreach ($resultGroup as $resultRow) {
                $validRows += (!empty($resultRow));
            }
        }

        $this->assertInternalType('array', $result);
        $this->assertCount($expectedErrors, $instance->getErrors());
        $this->assertEquals($expectedValidRows, $validRows);
    }

    /**
     * @return array
     */
    public function parseRowProvider()
    {
        return [
            'valid_row' => [
                ['*', '*', '*', '1.23', '2.34'],
                false,
                7
            ],
            'invalid_row' => [
                ['*', '*', '*', '34.56', 'non-integer'],
                true,
                0
            ],
        ];
    }

    /**
     * @param $csvRow
     * @param $expectedHasError
     * @param $expectedResultCount
     *
     * @dataProvider parseRowProvider
     */
    public function testParseRow($csvRow, $expectedHasError, $expectedResultCount)
    {
        $rowParser = $this->getObject(RowParser::class);
        $parserError = $this->getObject(ParseErrors::class);

        $instance = $this->getInstance(['rowParser' => $rowParser, 'parserErrors' => $parserError]);

        $parameters = [$csvRow, rand(0, 10), 'conditionName', 'fullConditionName', rand(1, 10)];
        $parsedRowResult = $this->invokeArgs('parseRow', $parameters, $instance);
        $hasErrorResult = $instance->hasErrors();

        $this->assertEquals($hasErrorResult, $expectedHasError);
        $this->assertInternalType('array', $parsedRowResult);
        $this->assertCount($expectedResultCount, $parsedRowResult);
    }

    /**
     * @return array
     */
    public function validateHeadersProvider()
    {
        return [
            'valid_headers' => [
                ['Header_1', 'Header_2', 'Header_3', 'Header_4', 'Header_5'],
                null
            ],
            'incomplete_headers' => [
                ['Header_6', 'Header_7'],
                '[POSTNL-0246] Invalid PostNL Table Rates File Format'
            ],
            'no_headers' => [
                false,
                '[POSTNL-0246] Invalid PostNL Table Rates File Format'
            ],
        ];
    }

    /**
     * @param $csvHeaders
     * @param $expectedException
     *
     * @dataProvider validateHeadersProvider
     */
    public function testValidateHeaders($csvHeaders, $expectedException)
    {
        $parserErrors = $this->getObject(ParseErrors::class);
        $instance = $this->getInstance(['parserErrors' => $parserErrors]);

        $readInterfaceMock = $this->getFakeMock(ReadInterface::class)->getMock();
        $readInterfaceMock->expects($this->once())->method('readCsv')->willReturn($csvHeaders);

        $this->invokeArgs('validateHeaders', [$readInterfaceMock], $instance);

        if (null === $expectedException) {
            $this->assertNull($expectedException);
        } else {
            $error = $instance->getErrors()[0];
            $this->assertEquals($expectedException, $error);
        }
    }

    /**
     * @return array
     */
    public function validateDuplicatesProvider()
    {
        return [
            'first_row_data' => [
                [
                    'dest_country_id' => 'abc',
                    'dest_region_id' => 'def',
                    'dest_zip' => 'ghi',
                    'condition_value' => 12.34
                ],
                [],
                5,
                ['abc-def-ghi-12.34' => 5]
            ],
            'non_duplicate_data' => [
                [
                    'dest_country_id' => 'abc',
                    'dest_region_id' => 'def',
                    'dest_zip' => 'ghi',
                    'condition_value' => 12.34
                ],
                ['jkl-mno-pqr-45.67' => 4],
                5,
                [
                    'jkl-mno-pqr-45.67' => 4,
                    'abc-def-ghi-12.34' => 5
                ]
            ],
            'duplicate_data' => [
                [
                    'dest_country_id' => 'abc',
                    'dest_region_id' => 'def',
                    'dest_zip' => 'ghi',
                    'condition_value' => 12.34
                ],
                ['abc-def-ghi-12.34' => 3],
                5,
                '[POSTNL-0250] Row #5 is a dupplicate of row #3'
            ],
        ];
    }

    /**
     * @param $newData
     * @param $validatedData
     * @param $rowCount
     * @param $expected
     *
     * @throws Exception
     *
     * @dataProvider validateDuplicatesProvider
     */
    public function testValidateDuplicates($newData, $validatedData, $rowCount, $expected)
    {
        $instance = $this->getInstance();
        $this->setProperty('validatedRows', $validatedData, $instance);

        try {
            $this->invokeArgs('validateDuplicates', [$newData, $rowCount], $instance);
            $result = $this->getProperty('validatedRows', $instance);

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

        $this->assertInternalType('string', $exceptionMessage);
        $this->assertEquals($expected, $exceptionMessage);
    }
}
