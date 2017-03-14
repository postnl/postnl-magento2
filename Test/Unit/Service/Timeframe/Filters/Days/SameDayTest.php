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
namespace TIG\PostNL\Unit\Service\Timeframe\Filters\Days;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Timeframe\Filters\Days\SameDay;
use TIG\PostNL\Helper\Data;

class SameDayTest extends TestCase
{
    protected $instanceClass = SameDay::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::sameday
     *
     * @param $today
     * @param $days
     * @param $expected
     */
    public function testFilter($today, $days, $expected)
    {
        $postNLHelper = $this->getFakeMock(Data::class)->getMock();

        $expectsWithValue = $postNLHelper->method('getDate');
        $expectsWithValue->willReturnCallback(function($date = '') use ($today) {
            return $date == '' ? $today : $date;
        });

        $instance = $this->getInstance(['helper' => $postNLHelper]);
        $result   = $instance->filter($days);

        $this->assertEquals($expected, $result);
    }
}
