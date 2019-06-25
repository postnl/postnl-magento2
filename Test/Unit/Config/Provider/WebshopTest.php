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
namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\Webshop;

class WebshopTest extends AbstractConfigurationTest
{
    protected $instanceClass = Webshop::class;

    public function testGetLabelSize()
    {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_WEBSHOP_LABEL_SIZE, 'A4');
        $this->assertEquals('A4', $instance->getLabelSize());
    }

    public function testGetShippingDuration() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_WEBSHOP_SHIPPING_DURATION, 1);
        $this->assertEquals('1', $instance->getShippingDuration());
    }

    public function testGetCutoffTime() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_WEBSHOP_CUTOFFTIME, '02:00:00');
        $this->assertEquals('02:00:00', $instance->getCutOffTime());
    }

    public function testGetSaturdayCutoffTime() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_WEBSHOP_SATURDAY_CUTOFFTIME, '20:00:00');
        $this->assertEquals('20:00:00', $instance->getSaturdayCutOffTime());
    }

    public function testGetSundayCutoffTime() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_WEBSHOP_SUNDAY_CUTOFFTIME, '20:00:00');
        $this->assertEquals('20:00:00', $instance->getSundayCutOffTime());
    }

    public function testGetShipmentDays() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_WEBSHOP_SHIPMENTDAYS, 1);
        $this->assertEquals(1, $instance->getShipmentDays());
    }

    public function testGetIsTrackAndTraceEnabled() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_TRACK_AND_TRACE_ENABLED, 1);
        $this->assertEquals(1, $instance->isTrackAndTraceEnabled());
    }

    public function testGetTrackAndTraceServiceUrl() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_TRACK_AND_TRACE_SERVICE_URL, 'https://postnl.nl/tracktrace/?');
        $this->assertEquals('https://postnl.nl/tracktrace/?', $instance->getTrackAndTraceServiceUrl());
    }

    public function testGetTrackAndTraceEmailTemplate() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_TRACK_AND_TRACE_MAIL_TEMPLATE, 'tig_postnl_postnl_settings_delivery_settings_track_and_trace_template');
        $this->assertEquals('tig_postnl_postnl_settings_delivery_settings_track_and_trace_template', $instance->getTrackAndTraceEmailTemplate());
    }

    public function testGetTrackAndTraceBccEmail() {
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_TRACK_AND_TRACE_BCC_EMAIL, 'test@test.com');
        $this->assertEquals('test@test.com', $instance->getTrackAndTraceBccEmail());
    }

    public function testGetAllowedShippingMethods()
    {
        $value = 'tig_postnl_regular,best_way';
        $instance = $this->getInstance();
        $this->setXpath(Webshop::XPATH_ADVANCED_ALLOWED_METHODS, $value);

        $expected = [
            'tig_postnl_regular',
            'best_way'
        ];

        $result = $instance->getAllowedShippingMethods();

        $this->assertEquals($expected, $result);
        $this->assertTrue(is_array($result));
    }

    public function cutoffTimeForDayProvider()
    {
        return [
            'CutoffTime for sundays, day number 7' => [
                '7', Webshop::XPATH_WEBSHOP_SUNDAY_CUTOFFTIME, '10:00:00'
            ],
            'CutoffTime for saturdays' => [
                '6', Webshop::XPATH_WEBSHOP_SATURDAY_CUTOFFTIME, '11:00:00'
            ],
            'CutoffTime for midweekdays' => [
                '4', Webshop::XPATH_WEBSHOP_CUTOFFTIME, '22:00:00'
            ],
            'CutoffTime when day is null' => [
                null, Webshop::XPATH_WEBSHOP_CUTOFFTIME, '22:00:00'
            ]
        ];
    }

    /**
     * @param $day
     * @param $xpath
     * @param $cutoffTime
     *
     * @dataProvider cutoffTimeForDayProvider
     */
    public function testGetCutOffTimeForDay($day, $xpath, $cutoffTime)
    {
        $instance = $this->getInstance();
        $this->setXpath($xpath, $cutoffTime);

        $result = $instance->getCutOffTimeForDay($day);
        $this->assertEquals($cutoffTime, $result);
    }
}
