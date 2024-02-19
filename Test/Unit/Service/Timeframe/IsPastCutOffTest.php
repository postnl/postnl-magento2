<?php

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
