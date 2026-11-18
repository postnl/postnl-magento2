<?php

namespace TIG\PostNL\Test\Fixtures\Timeframes\Options;

class DataProvider
{
    public static function allOptions()
    {
        return [
            'when all options are enabled'  => [true, self::getOptions(), self::getOptions()],
            'when all options are disabled' => [false, self::getOptions(), self::getOptionsWhenAllAreDisabled()]
        ];
    }

    public static function evening()
    {
        return [
            'evening is disabled' => [false, self::getOptions(), self::getOptionsForEveningDisabled()],
            'evening is enabled'  => [true, self::getOptions(), self::getOptions()]
        ];
    }

    private static function getOptions()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '18:00:00',
                'Options' => (object)[
                    'string' => ['Evening']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private static function getOptionsForEveningDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ]
        ];
    }

    private static function getOptionsWhenAllAreDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ]
        ];
    }
}
