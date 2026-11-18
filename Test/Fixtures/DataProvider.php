<?php

namespace TIG\PostNL\Test\Fixtures;

class DataProvider
{
    /**
     * @return \Generator
     */
    public static function randomWordsProvider()
    {
        for ($i = 0; $i <= 3; $i++) {
            yield [uniqid()];
        }
    }

    /**
     * @return array
     */
    public static function enabledAndDisabled()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @return string
     */
    public static function liveStagingProvider()
    {
        return [
            ['0', 'off'],
            ['1', 'live'],
            ['2', 'staging'],
        ];
    }

    /**
     * @return array
     */
    public static function pdfLabelPaths()
    {
        return [
            [
                [
                    __DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf',
                    __DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-2.pdf'
                ]
            ],
            [
                [
                    __DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf'
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public static function pdfLabelFiles()
    {
        return [
            [
                [
                    base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf')),
                    base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-2.pdf'))
                ]
            ],
            [
                [
                    base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf'))
                ]
            ],
        ];
    }
}
