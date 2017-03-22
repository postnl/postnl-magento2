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
namespace TIG\PostNL\Test\Unit\Service\Timeframe\Filters\Days;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Timeframe\Filters\Days\CutOffTimes;
use TIG\PostNL\Test\TestCase;

class CutOffTimesTest extends TestCase
{
    public $instanceClass = CutOffTimes::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::noFiltering
     *
     * @param $input
     * @param $output
     */
    public function testDoesNotFilterWhenBeforeCutOff($input, $output)
    {
        $this->assertEquals($output, $this->loadInstance('19:00:00')->filter($input));
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::cutOffTimePassed
     *
     * @param $input
     * @param $output
     */
    public function testDoesFilterAfterCutOff($input, $output)
    {
        $this->assertEquals($output, $this->loadInstance('10:00:00')->filter($input));
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::cutOffNextDayRemoved
     *
     * @param $input
     * @param $output
     */
    public function testDoesFilterOnlyTheNextDay($input, $output)
    {
        $result = $this->loadInstance('10:00:00')->filter($input);
        $this->assertEquals($output, $result);
    }

    /**
     * @param $cutOffTime
     *
     * @return CutOffTimes
     */
    private function loadInstance($cutOffTime)
    {
        $webshopMock = $this->getFakeMock(Webshop::class, true);
        $this->mockFunction($webshopMock, 'getCutOffTime', '18:00:00');

        $currentDate = new \DateTime('18-11-2016 18:00:00');
        $cutOffDateTime = new \DateTime('18-11-2016 ' . $cutOffTime);
        $todayDateTime = new \DateTime('18-11-2016');

        $currentDateMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($currentDateMock, 'date', $currentDate);

        $cutOffTimeMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($cutOffTimeMock, 'date', $cutOffDateTime);

        $todayMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($todayMock, 'date', $todayDateTime);

        $dateLoaderMock = $this->getMock(TimezoneInterface::class);
        $dateMethod = $dateLoaderMock->method('date');
        $dateMethod->willReturnCallback(function ($date) {
            return new \DateTime($date);
        });

        return $this->getInstance([
            'webshop' => $webshopMock,
            'cutOffTime' => $cutOffTimeMock,
            'currentDate' => $currentDateMock,
            'dateLoader' => $dateLoaderMock,
            'today' => $todayMock,
        ]);
    }
}
