<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Unit\Block\Adminhtml\Renderer;

use TIG\PostNL\Block\Adminhtml\Renderer\ShippingDate;
use TIG\PostNL\Test\TestCase;
use \Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class ShippingDateTest extends TestCase
{
    protected $instanceClass = ShippingDate::class;

    public function formatShippingDateProvider()
    {
        return [
            [0, 'Today'],
            [-10, '19 Nov. 2016'],
            [10, 'In 10 days'],
            [1, 'Tomorrow'],
        ];
    }
    /**
     * @param $daysToGo
     * @param $expected
     *
     * @dataProvider formatShippingDateProvider
     */
    public function testFormatShippingDate($daysToGo, $expected)
    {
        $diff = new \stdClass();
        $diff->days = $daysToGo;
        $diff->invert = $daysToGo < 0;
        $instance = $this->getInstance();
        $timezoneInterface = $this->getMock(TimezoneInterface::class);
        $this->setProperty('timezoneInterface', $timezoneInterface, $instance);
        $dateMock = $this->getMock(\DateTime::class);
        $diffMock = $dateMock->expects($this->once());
        $diffMock->method('diff');
        $diffMock->willReturn($diff);
        $dateExpects = $timezoneInterface->expects($this->exactly(2));
        $dateExpects->method('date');
        $dateExpects->withConsecutive(
            [null, null, true],
            ['2016-11-19', null, true]
        );
        $dateExpects->willReturn($dateMock);
        $formatExpects = $dateMock->expects($this->any());
        $formatExpects->method('format');
        $formatExpects->willReturn('19 Nov. 2016');
        $result = $this->invokeArgs('formatShippingDate', ['2016-11-19'], $instance);
        if ($result instanceof Phrase) {
            $result = $result->render();
        }
        $this->assertEquals($expected, $result);
    }
    public function getShipAtProvider()
    {
        return [
            [true, '2016-11-19'],
            [false, '2016-11-19'],
            [true, null],
            [false, null],
        ];
    }

    /**
     * @param $useObject
     * @param $shipAt
     *
     * @dataProvider getShipAtProvider
     */
    public function testGetShipAt($useObject, $shipAt)
    {
        $input = $shipAt;
        if ($useObject) {
            $shipment = $this->getFakeMock(\TIG\PostNL\Model\Shipment::class)->setMethods(['getShipAt'])->getMock();

            $shipAtExpects = $shipment->expects($this->once());
            $shipAtExpects->method('getShipAt');
            $shipAtExpects->willReturn($shipAt);

            $input = $shipment;
        }

        $result = $this->invokeArgs('getShipAt', [$input]);
        $this->assertEquals($shipAt, $result);
    }
}
