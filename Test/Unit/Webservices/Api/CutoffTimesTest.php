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

        $getCutoffTimeExpects = $webshopConfigMock->expects($this->exactly(5));
        $getCutoffTimeExpects->method('getCutOffTime');
        $getCutoffTimeExpects->willReturn($cutoffTime);

        $getSundayCutOffTimeExpects = $webshopConfigMock->expects($this->once());
        $getSundayCutOffTimeExpects->method('getSundayCutOffTime');
        $getSundayCutOffTimeExpects->willReturn($cutoffTime);

        $getSaturdayCutoffTimesExpects = $webshopConfigMock->expects($this->once());
        $getSaturdayCutoffTimesExpects->method('getSaturdayCutOffTime');
        $getSaturdayCutoffTimesExpects->willReturn($cutoffTime);

        $instance = $this->getInstance([
            'webshopSettings' => $webshopConfigMock
        ]);

        $result = $instance->get();

        $this->assertEquals($expects, $result);
    }
}