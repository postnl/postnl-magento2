<?php

namespace TIG\PostNL\Test\Unit\Webservices;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Api\Exception as PostNLException;
use TIG\PostNL\Webservices\ExceptionHandler;

class ExceptionHandlerTest extends TestCase
{
    protected $instanceClass = ExceptionHandler::class;

    public function hasCifExceptionProvider()
    {
        return [
            ['random error', false],
            ['Check CIFException in the detail section', true],
        ];
    }

    /**
     * @param $message
     * @param $expected
     *
     * @dataProvider hasCifExceptionProvider
     */
    public function testHasCifException($message, $expected)
    {
        $soapFault = new \SoapFault('test', $message);
        $instance = $this->getInstance();
        $this->setProperty('soapFault', $soapFault, $instance);

        $result = $this->invoke('hasCifException', $instance);
        $this->assertEquals($expected, $result);
    }

    public function hasValidErrorsOnlyProvider()
    {
        return [
            'contains a valid error' => [
                'errors' => [
                    ['message' => 'error message', 'number' => ExceptionHandler::SHIPMENT_NOT_FOUND_ERROR_NUMBER],
                    ['message' => 'error message', 'number' => '18'],
                ],
                'xml' => 'notempty',
                'expected' => false
            ],
            'no valid errors' => [
                'errors' => [
                    ['message' => 'error message', 'number' => ExceptionHandler::SHIPMENT_NOT_FOUND_ERROR_NUMBER],
                    ['message' => 'error message 2', 'number' => ExceptionHandler::SHIPMENT_NOT_FOUND_ERROR_NUMBER],
                ],
                'xml' => 'notempty',
                'expected' => true
            ],
            'only valid errors' => [
                'errors' => [
                    ['message' => 'error message', 'number' => 10],
                    ['message' => 'error message 2', 'number' => 10],
                ],
                'xml' => 'notempty',
                'expected' => false
            ],
            'empty xml' => [
                'errors' => [],
                'xml' => '',
                'expected' => false
            ],
        ];
    }

    /**
     * @dataProvider hasValidErrorsOnlyProvider
     *
     * @param $errors
     * @param $xml
     * @param $expected
     */
    public function testHasValidErrorsOnly($errors, $xml, $expected)
    {
        $instance = $this->getInstance();
        $this->setProperty('errors', $errors, $instance);
        $this->setProperty('responseXml', $xml, $instance);

        $result = $this->invoke('hasValidErrorsOnly', $instance);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testAddCifErrorsToException($error)
    {
        $exception = $this->getFakeMock(PostNLException::class)->getMock();

        $addErrorExpects = $exception->expects($this->once());
        $addErrorExpects->method('addError');
        $addErrorExpects->with($error . ': ' . $error);

        $instance = $this->getInstance();
        $this->setProperty('errors', [['message' => $error, 'number' => $error]], $instance);
        $this->setProperty('exception', $exception, $instance);

        $result = $this->invoke('addCifErrorsToException', $instance);
    }
}
