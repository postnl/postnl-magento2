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

namespace TIG\PostNL\Test\Integration\Controller\DeliveryOptions;

class TimeframesTest extends TestBase
{
    public function testResponseJsonContainsTheCorrectKeys()
    {
        $this->dispatch('postnl/deliveryoptions/timeframes');

        $response = $this->getResponse()->getBody();
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertJson($response);

        $json = json_decode($response, true);
        $this->assertArrayHasKey('timeframes', $json);
        $this->assertArrayHasKey('price', $json);
    }

    public function testTheResponseContainsTheCorrectPrice()
    {
        $calculator = $this->getMockBuilder(\TIG\PostNL\Service\Carrier\Price\Calculator::class);
        $calculator->disableOriginalConstructor();
        $calculator = $calculator->getMock();

        $this->_objectManager->configure([
            'preferences' => [
                \TIG\PostNL\Service\Carrier\Price\Calculator::class => get_class($calculator),
            ],
        ]);

        $calculator = $this->_objectManager->get(\TIG\PostNL\Service\Carrier\Price\Calculator::class);
        $calculator->method('price')->willReturn(['price' => 123, 'cost' => 0]);

        $this->dispatch('postnl/deliveryoptions/timeframes');
        $response = $this->getResponse()->getBody();
        $json = json_decode($response, true);

        $this->assertEquals(123, $json['price']);
    }
}
