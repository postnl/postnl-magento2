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
namespace TIG\PostNL\Test\Unit\Webservices\Api;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Api\Exception as PostNLException;

class ExceptionTest extends TestCase
{
    public function getMessageProvider()
    {
        return [
            'empty_xml' => [
                'requestXml' => null,
                'responseXml' => null,
                'errors' => [],
                'message' => 'There was an error',
                'expected' => 'There was an error',
            ],
            'filled_request_xml' => [
                'requestXml' => '<?xml version="1.0"?><test></test>',
                'responseXml' => null,
                'errors' => [],
                'message' => 'There was an error.',
                'expected' => 'There was an error.

<<<< Request XML >>>>
<?xml version="1.0"?><test></test>',
            ],
            'filled_request_and_response_xml' => [
                'requestXml' => '<?xml version="1.0"?><test></test>',
                'responseXml' => '<?xml version="1.0"?><test></test>',
                'errors' => [],
                'message' => 'There was an error.',
                'expected' => 'There was an error.

<<<< Request XML >>>>
<?xml version="1.0"?><test></test>

<<<< Response XML >>>>
<?xml version="1.0"?><test></test>',
            ],
            'with_errors' => [
                'requestXml' => '',
                'responseXml' => '',
                'errors' => [
                    'error 1',
                    'error 2',
                ],
                'message' => 'There was an error:',
                'expected' => 'There was an error:

- error 1
- error 2',
            ],
            'with_errors_and_xml' => [
                'requestXml' => '<?xml version="1.0"?><test></test>',
                'responseXml' => '<?xml version="1.0"?><test></test>',
                'errors' => [
                    'error 1',
                    'error 2',
                ],
                'message' => 'There was an error:',
                'expected' => 'There was an error:

- error 1
- error 2

<<<< Request XML >>>>
<?xml version="1.0"?><test></test>

<<<< Response XML >>>>
<?xml version="1.0"?><test></test>',
            ],
        ];
    }

    /**
     * @param $requestXml
     * @param $responseXml
     * @param $message
     * @param $expected
     *
     * @dataProvider getMessageProvider
     */
    public function testGetMessage($requestXml, $responseXml, $errors, $message, $expected)
    {
        $exception = new PostNLException($message);

        if ($requestXml !== null) {
            $exception->setRequestXml($requestXml);
        }

        if ($responseXml !== null) {
            $exception->setResponseXml($responseXml);
        }

        foreach ($errors as $error) {
            $exception->addError($error);
        }

        $result = $exception->getMessage();

        $this->assertEquals($expected, $result);
    }

    public function testGetRequestXml()
    {
        $exception = new PostNLException('test');

        $xml = 'testxml';
        $exception->setRequestXml($xml);

        $this->assertEquals($xml, $exception->getRequestXml());
    }

    public function testGetResponseXml()
    {
        $exception = new PostNLException('test');

        $xml = 'testxml';
        $exception->setResponseXml($xml);

        $this->assertEquals($xml, $exception->getResponseXml());
    }
}
