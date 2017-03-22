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
use TIG\PostNL\Service\Timeframe\Filters\Days\ShipmentDays;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Helper\Data;

class ShipmentDaysTest extends TestCase
{
    protected $instanceClass = ShipmentDays::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::shipmentDays
     *
     * @param $shipmentDays
     * @param $days
     * @param $delay
     * @param $expected
     *
     * @throws \Exception
     */
    public function testFilter($shipmentDays, $days, $delay, $expected)
    {
        $webshopSettings = $this->getFakeMock(Webshop::class)->getMock();
        $this->mockFunction($webshopSettings, 'getShipmentDays', $shipmentDays);
        $this->mockFunction($webshopSettings, 'getCutOffTime', '18:00:00');

        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $this->mockFunction($shippingOptions, 'getDeliveryDelay', $delay);

        $postNLHelper = $this->getObject(Data::class);

        $instance = $this->getInstance([
            'webshop' => $webshopSettings,
            'shippingOptions' => $shippingOptions,
            'data' => $postNLHelper
        ]);

        $result = $instance->filter($days);

        $this->assertEquals($expected, $result);
    }
}
