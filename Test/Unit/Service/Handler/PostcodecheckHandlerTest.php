<?php

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
