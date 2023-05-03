<?php

namespace TIG\PostNL\Test\Fixtures;

class DataProvider
{
    /**
     * @return \Generator
     */
    public function randomWordsProvider()
    {
        for ($i = 0; $i <= 3; $i++) {
            yield [uniqid()];
        }
    }

    /**
     * @return array
     */
    public function enabledAndDisabled()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @return string
     */
    public function liveStagingProvider()
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
    public function pdfLabelPaths()
    {
        return [
            [
                'separate_pdfs' => [
                    __DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf',
                    __DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-2.pdf'
                ]
            ],
            [
                'separate_pdfs' => [
                    __DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf'
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function pdfLabelFiles()
    {
        return [
            [
                'separate_pdfs' => [
                    base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf')),
                    base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-2.pdf'))
                ]
            ],
            [
                'separate_pdfs' => [
                    base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'shippinglabel-1.pdf'))
                ]
            ],
        ];
    }
}
