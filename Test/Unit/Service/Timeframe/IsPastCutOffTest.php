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
namespace TIG\PostNL\Test\Unit\Service\Timeframe;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;
use TIG\PostNL\Helper\Data as PostNLHelper;

class IsPastCutOffTest extends TestCase
{
    public $instanceClass = IsPastCutOff::class;

    public function testBeforeCutOff()
    {
        $result = $this->prepareInstance(true);

        $this->assertFalse($result);
    }

    public function testAfterCutOff()
    {
        $result = $this->prepareInstance(false);

        $this->assertTrue($result);
    }

    /**
     * @param $beforeCutOff
     *
     * @return bool
     */
    private function prepareInstance($beforeCutOff)
    {
        $helperMock = $this->getFakeMock(PostNLHelper::class, true);
        $this->mockFunction($helperMock, 'getDayOrWeekNumber', '4');

        $webshopMock = $this->getFakeMock(Webshop::class, true);
        $this->mockFunction($webshopMock, 'getCutOffTimeForDay', date('H:30:00'), ['4']);

        $nowDate = new \DateTime('today ' . date('H:' . ($beforeCutOff ? '25' : '35') . ':00'));
        $timezoneInterfaceMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($timezoneInterfaceMock, 'date', $nowDate, ['now', null, false, false]);

        /** @var IsPastCutOff $instance */
        $instance = $this->getInstance([
            'webshop'      => $webshopMock,
            'currentDate'  => $timezoneInterfaceMock,
            'postNLHelper' => $helperMock
        ]);

        $result = $instance->calculate();

        return $result;
    }
}
