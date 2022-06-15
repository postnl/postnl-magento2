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
namespace TIG\PostNL\Unit\Service\Import;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;
use Magento\Framework\Filesystem\File\ReadInterface as FileReadInterface;
use TIG\PostNL\Service\Import\Csv;
use TIG\PostNL\Test\TestCase;

class CsvTest extends TestCase
{
    protected $instanceClass = Csv::class;

    /**
     * @return array
     */
    public function getDataProvider()
    {
        return [
            'no_records' => [
                []
            ],
            'single_record' => [
                [
                    ['record_1_1', 'record_1_2']
                ]
            ],
            'multiple_records' => [
                [
                    ['record_2_1', 'record_2_2'],
                    ['record_3_1', 'record_3_2'],
                    ['record_4_1', 'record_4_2']
                ]
            ],
        ];
    }

    /**
     * @param $records
     *
     * @dataProvider getDataProvider
     */
    public function testGetData($records)
    {
        $fileReadInterface = $this->getMockBuilder(FileReadInterface::class)->getMock();

        $dirReadInterface = $this->getMockBuilder(DirectoryReadInterface::class)->getMock();
        $dirReadInterface->expects($this->any())->method('openFile')->willReturn($fileReadInterface);

        $filesystemMock = $this->getFakeMock(Filesystem::class)->setMethods(['getDirectoryRead'])->getMock();
        $filesystemMock->expects($this->any())->method('getDirectoryRead')->willReturn($dirReadInterface);

        $fileParserMock = $this->getFakeMock(Csv\FileParser::class)
            ->setMethods(['getRows', 'getColumns', 'hasErrors'])
            ->getMock();
        $fileParserMock->expects($this->once())->method('getColumns');
        $fileParserMock->expects($this->once())->method('getRows')->willReturn($records);
        $fileParserMock->expects($this->once())->method('hasErrors')->willReturn(false);

        $instance = $this->getInstance(['filesystem' => $filesystemMock, 'fileParser' => $fileParserMock]);
        $result = $instance->getData('somefile.csv', 1, 'package_value');

        $this->assertIsArray($result);

        $this->assertArrayHasKey('columns', $result);
        $this->assertArrayHasKey('records', $result);

        $this->assertEquals($records, $result['records']);
    }

    /**
     * @return array
     */
    public function checkImportErrorsProvider()
    {
        return [
            'no_errors' => [
                false,
                []
            ],
            'single_error' => [
                true,
                ['Error abc']
            ],
            'multiple_errors' => [
                true,
                ['Error def', 'Error ghi']
            ]
        ];
    }

    /**
     * @param $hasErrors
     * @param $errorMessages
     *
     * @throws LocalizedException
     * @throws \Exception
     *
     * @dataProvider checkImportErrorsProvider
     */
    public function testCheckImportErrors($hasErrors, $errorMessages)
    {
        $fileParserMock = $this->getFakeMock(Csv\FileParser::class)->setMethods(['hasErrors', 'getErrors'])->getMock();
        $fileParserMock->expects($this->once())->method('hasErrors')->willReturn($hasErrors);
        $fileParserMock->expects($this->exactly((int)$hasErrors))->method('getErrors')->willReturn($errorMessages);

        $instance = $this->getInstance(['fileParser' => $fileParserMock]);

        $expectedErrorMessage = '[POSTNL-0196] File has not been imported. See the following list of errors: ';
        $expectedErrorMessage .= implode(" \n", $errorMessages);

        try {
            $this->invoke('checkImportErrors', $instance);
            $this->assertFalse($hasErrors);
        } catch (LocalizedException $exception) {
            if (!$hasErrors) {
                throw $exception;
            }

            $this->assertTrue($hasErrors);
            $this->assertEquals($expectedErrorMessage, $exception->getMessage());
        }
    }
}
