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

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Import\ParseErrors;

class ParseErrorsTest extends TestCase
{
    protected $instanceClass = ParseErrors::class;

    /**
     * @return array
     */
    public function errorsProvider()
    {
        return [
            'no_errors' => [
                [],
                0
            ],
            'single_error' => [
                ['Missing header column "Price"'],
                1
            ],
            'multiple_errors' => [
                [
                    'Missing header column "ZipCode"',
                    'Invalid value "price" on row #1',
                    'Please reformat the tablerate'
                ],
                3
            ]
        ];
    }

    /**
     * @param $errors
     * @param $expected
     *
     * @dataProvider errorsProvider
     */
    public function testGetErrorCount($errors, $expected)
    {
        $instance = $this->getInstance();

        foreach ($errors as $error) {
            $instance->addError($error);
        }

        $result = $instance->getErrorCount();
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $errors
     * @param $expected
     *
     * @dataProvider errorsProvider
     */
    public function testGetErrors($errors, $expected)
    {
        $instance = $this->getInstance();

        foreach ($errors as $error) {
            $instance->addError($error);
        }

        $result = $instance->getErrors();
        $this->assertCount($expected, $result);
        $this->assertEquals($errors, $result);
    }

    /**
     * @param $errors
     * @param $expected
     *
     * @dataProvider errorsProvider
     */
    public function testResetErrors($errors, $expected)
    {
        $instance = $this->getInstance();

        foreach ($errors as $error) {
            $instance->addError($error);
        }

        $errorCountBeforeReset = $instance->getErrorCount();
        $this->assertEquals($expected, $errorCountBeforeReset);

        $instance->resetErrors();
        $errorCountAfterReset = $instance->getErrorCount();
        $this->assertEquals(0, $errorCountAfterReset);
    }

    public function testAddErrors()
    {
        $errors = [
            'error 1',
            'error 2',
        ];

        /** @var ParseErrors $instance */
        $instance = $this->getInstance();
        $instance->addErrors($errors);

        $this->assertEquals(2, $instance->getErrorCount());
    }
}
