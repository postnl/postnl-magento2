<?php

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
