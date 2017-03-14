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

    public function shipmentDays()
    {
        return [
            'Filter days not beyond cutoff' => [
                '09-03-2017',
                $this->getDaysForShipmentDays(),
                '10:00:00',
                '1',
                $this->getShipmentDaysBeforeCutoffTime()
            ],
            'Filter days beyond cutoff' => [
                '09-03-2017',
                $this->getDaysForShipmentDays(),
                '20:00:00',
                '1',
                $this->getShipmentDaysBeyondCutoff()
            ]
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

    private function getShipmentDaysBeyondCutoff()
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
            ],
            (object)[
                'Date' => '11-03-2017',
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
            ]
        ];
    }

    private function getShipmentDaysBeforeCutoffTime()
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
            ],
            (object)[
                'Date' => '11-03-2017',
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
            ]
        ];
    }

    private function getDaysForShipmentDays()
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
            ],
            (object)[
                'Date' => '11-03-2017',
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
            (object)[
                'Date' => '12-03-2017',
                'Timeframes' => (object)[
                    'TimeframeTimeFrame' => [
                        (object)[
                            'From' => '13:00:00',
                            'Options' => (object)[
                                'string' => ['Sunday']
                            ],
                            'To' => '15:30:00'
                        ],
                        (object)[
                            'From' => '18:00:00',
                            'Options' => (object)[
                                'string' => ['Sunday']
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
}