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
     * @param $cutOffTime
     *
     * @return CutOffTimes
     */
    private function loadInstance($cutOffTime)
    {
        $currentDate = new \DateTime('19-11-2016 18:00:00');
        $cutOffDateTime = new \DateTime('19-11-2016 ' . $cutOffTime);

        $currentDateMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($currentDateMock, 'date', $currentDate);

        $cutOffTimeMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($cutOffTimeMock, 'date', $cutOffDateTime);

        return $this->getInstance([
            'cutOffTime' => $cutOffTimeMock,
            'currentDate' => $currentDateMock,
        ]);
    }
}
