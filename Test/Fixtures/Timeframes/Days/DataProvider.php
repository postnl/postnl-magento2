<?php

namespace TIG\PostNL\Test\Fixtures\Timeframes\Days;

class DataProvider
{
    public static function sameday()
    {
        return [
            'has sameday'     => [
                '09-03-2017',
                self::getDaysForSameDay(),
                self::getDaysForIsSameDayTrue(),
            ],
            'has not sameday' => [
                '08-03-2017',
                self::getDaysForSameDay(),
                self::getDaysForSameDay(),
            ]
        ];
    }

    public static function noFiltering()
    {
        return [
            [
                self::getDaysForTwoWeeks(),
                self::getDaysForTwoWeeks(),
            ],
        ];
    }

    public static function shipmentDays()
    {
        return [
            'Wednesday is not a shippingday' => [
                '1,2,4,5,6,7',
                self::getDaysForTwoWeeks(),
                '1',
                self::getDaysForTwoWeeksWednesdayDisabled()
            ],
        ];
    }

    private static function getDaysForSameDay()
    {
        return [
            (object)[
                'Date' => '09-03-2017',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[
                'Date' => '10-03-2017',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ]
        ];
    }

    private static function getDaysForIsSameDayTrue()
    {
        return [
            (object)[
                'Date' => '10-03-2017',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ]
        ];
    }

    private static function getDaysForTwoWeeks()
    {
        return [
            (object)[ // Saturday
                'Date' => '19-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Monday
                'Date' => '21-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Tuesday
                'Date' => '22-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Wednesday
                'Date' => '23-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Thursday
                'Date' => '24-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Friday
                'Date' => '25-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Saturday
                'Date' => '26-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Monday
                'Date' => '28-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Tuesday
                'Date' => '29-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Wednesday
                'Date' => '30-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Thursday
                'Date' => '01-12-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Friday
                'Date' => '02-12-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
        ];
    }

    private static function getDaysForTwoWeeksWednesdayDisabled()
    {
        return [
            (object)[ // Saturday
                'Date' => '19-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Monday
                'Date' => '21-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Tuesday
                'Date' => '22-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Wednesday
                'Date' => '23-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Friday
                'Date' => '25-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Saturday
                'Date' => '26-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Monday
                'Date' => '28-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Tuesday
                'Date' => '29-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Daytime']
                            ],
                            'To' => '15:30:00'
                        ]
                    ]
                ]
            ],
            (object)[ // Wednesday
                'Date' => '30-11-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
            (object)[ // Friday
                'Date' => '02-12-2016',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
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
                    ]
                ]
            ],
        ];
    }

    private static function getDaysForTwoWeeksCutOffRemoved()
    {
        $shipmentDays = self::getDaysForTwoWeeks();
        unset($shipmentDays[0]);

        return array_values($shipmentDays);
    }
}
