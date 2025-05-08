<?php

namespace TIG\PostNL\Test\Fixtures\Timeframes\Days;

class DataProvider
{
    public function sameday()
    {
        return [
            'has sameday'     => [
                '09-03-2017',
                $this->getDaysForSameDay(),
                $this->getDaysForIsSameDayTrue(),
            ],
            'has not sameday' => [
                '08-03-2017',
                $this->getDaysForSameDay(),
                $this->getDaysForSameDay(),
            ]
        ];
    }

    public function noFiltering()
    {
        return [
            [
                $this->getDaysForTwoWeeks(),
                $this->getDaysForTwoWeeks(),
            ],
        ];
    }

    public function shipmentDays()
    {
        return [
            'Wednesday is not a shippingday' => [
                '1,2,4,5,6,7',
                $this->getDaysForTwoWeeks(),
                '1',
                $this->getDaysForTwoWeeksWednesdayDisabled()
            ],
        ];
    }

    private function getDaysForSameDay()
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

    private function getDaysForIsSameDayTrue()
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

    private function getDaysForTwoWeeks()
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

    private function getDaysForTwoWeeksWednesdayDisabled()
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

    private function getDaysForTwoWeeksCutOffRemoved()
    {
        $shipmentDays = $this->getDaysForTwoWeeks();
        unset($shipmentDays[0]);

        return array_values($shipmentDays);
    }
}
