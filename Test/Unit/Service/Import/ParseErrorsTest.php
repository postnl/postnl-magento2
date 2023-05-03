<?php

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
