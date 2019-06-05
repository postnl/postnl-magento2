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
namespace TIG\PostNL\Test\Unit\Service\Handler;

use TIG\PostNL\Service\Handler\PostcodecheckHandler;
use TIG\PostNL\Test\TestCase;

class PostcodecheckHandlerTest extends TestCase
{
    protected $instanceClass = PostcodecheckHandler::class;

    public function requestDataProvider()
    {
        return [
            'Correct Request data' => [
                ['postcode' => '1014BA', 'housenumber' => '37'],
                ['postalcode' => '1014BA', 'housenumber' => '37'],
            ],
            'Incorrect keys in Data array' => [
                ['zipcode' => '1014BA', 'huisnummer' => '37'],
                false,
            ],
        ];
    }

    /**
     * @dataProvider requestDataProvider
     * @param $data
     * @param $expected
     */
    public function testConvertRequest($data, $expected)
    {
        $instance = $this->getInstance();

        $this->assertSame($expected, $instance->convertRequest($data));
    }

    public function responseDataProvider()
    {
        return [
            'Correct Response Data' => [
                '[{"status":1,"streetName":"Kabelweg","city":"Amsterdam"}]',
                ['status' => 1, 'streetName' => 'Kabelweg', 'city' => 'Amsterdam'],
            ],
            'In correct Response Data' => [
                '[{"status":1,"city":"Amsterdam"}]',
                false,
            ],
            'errors param set' => [
                '{"0":{"status":1,"streetName":"Kabelweg","city":"Amsterdam"},"errors":"error message"}',
                'error',
            ],
            'fault param set' => [
                '{"0":{"status":1,"streetName":"Kabelweg","city":"Amsterdam"},"fault":"fault message"}',
                'error',
            ],
            'no error, fault or 0 param set' => [
                '{"random":"different params than expected"}',
                'error',
            ],
            'empty param array' => [
                '[]',
                false,
            ],
            'empty params' => [
                '',
                'error',
            ]
        ];
    }

    /**
     * @dataProvider responseDataProvider
     * @param $data
     * @param $expected
     */
    public function testConvertResponse($data, $expected)
    {
        $instance = $this->getInstance();

        $this->assertSame($expected, $instance->convertResponse($data));
    }
}
