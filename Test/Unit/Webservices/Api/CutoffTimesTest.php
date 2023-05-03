<?php

namespace TIG\PostNL\Test\Unit\Webservices\Api;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Api\CutoffTimes;
use TIG\PostNL\Config\Provider\Webshop;

class CutoffTimesTest extends TestCase
{
    protected $instanceClass = CutoffTimes::class;

    public function getProvider()
    {
        return [
            'Sunday and Monday closed' => [
                '2,3,4,5,6',
                '20:00:00',
                [
                    [
                        'Day' => '01',
                        'Time' => '20:00:00',
                        'Available' => '0'
                    ],
                    [
                        'Day' => '02',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '03',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '04',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '05',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '06',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '07',
                        'Time' => '20:00:00',
                        'Available' => '0'
                    ]
                ]
            ],
            'All days open' => [
                '1,2,3,4,5,6,0',
                '20:00:00',
                [
                    [
                        'Day' => '01',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '02',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '03',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '04',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '05',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '06',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ],
                    [
                        'Day' => '07',
                        'Time' => '20:00:00',
                        'Available' => '1'
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $shipmentDays
     * @param $cutoffTime
     * @param $expects
     *
     * @dataProvider getProvider
     */
    public function testGet($shipmentDays, $cutoffTime, $expects)
    {
        $webshopConfigMock = $this->getFakeMock(Webshop::class)->getMock();

        $getShipmentDaysExpects = $webshopConfigMock->expects($this->exactly(7));
        $getShipmentDaysExpects->method('getShipmentDays');
        $getShipmentDaysExpects->willReturn($shipmentDays);

        $getCutoffTimeExpects = $webshopConfigMock->expects($this->exactly(7));
        $getCutoffTimeExpects->method('getCutOffTimeForDay');
        $getCutoffTimeExpects->willReturn($cutoffTime);

        $instance = $this->getInstance([
            'webshopSettings' => $webshopConfigMock
        ]);

        $result = $instance->get();

        $this->assertCount(7, $result);
        $this->assertEquals($expects, $result);
    }
}