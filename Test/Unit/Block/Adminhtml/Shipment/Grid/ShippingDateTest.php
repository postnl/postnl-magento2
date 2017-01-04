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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Shipment\Grid;

use \Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use TIG\PostNL\Block\Adminhtml\Shipment\Grid\ShippingDate;
use TIG\PostNL\Test\TestCase;

class ShippingDateTest extends TestCase
{
    protected $instanceClass = ShippingDate::class;

    public function getInstance(array $args = [])
    {
        if (!isset($args['context'])) {
            $contextMock = $this->getMockForAbstractClass(ContextInterface::class, [], '', false, true, true, []);
            $processor   = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
                ->disableOriginalConstructor()
                ->getMock();
            $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);

            $args['context'] = $contextMock;
        }

        return parent::getInstance($args);
    }

    public function formatShippingDateProvider()
    {
        return [
            ['2016-11-19', 0, 'Today'],
            ['2016-11-19', -10, '19 Nov. 2016'],
            ['2016-11-19', 10, 'In 10 days'],
            ['2016-11-19', 1, 'In 1 day'],
        ];
    }

    /**
     * @param $shipAt
     * @param $daysToGo
     * @param $expected
     *
     * @dataProvider formatShippingDateProvider
     */
    public function testFormatShippingDate($shipAt, $daysToGo, $expected)
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
            ['2016-11-19'],
            [null],
        ];
    }

    /**
     * @dataProvider getShipAtProvider
     */
    public function testGetShipAt($shipAt)
    {
        $result = $this->invokeArgs('getShipAt', [['tig_postnl_ship_at' => $shipAt]]);

        $this->assertEquals($shipAt, $result);
    }
}
