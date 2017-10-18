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

use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Webservices\Endpoints\Locations;
use TIG\PostNL\Webservices\Endpoints\TimeFrame;
use Magento\TestFramework\TestCase\AbstractController;

class TestBase extends AbstractController
{
    protected function setUp()
    {
        if (getenv('TRAVIS') !== false) {
            $this->markTestSkipped('Fails on Travis');
        }

        parent::setUp();

        $addressEnhancer = $this->getMock(\TIG\PostNL\Helper\AddressEnhancer::class);
        $deliveryDate = $this->getMockBuilder(DeliveryDate::class)->disableOriginalConstructor()->getMock();
        $timeFrame = $this->getMockBuilder(TimeFrame::class)->disableOriginalConstructor()->getMock();
        $location = $this->getMockBuilder(Locations::class)->disableOriginalConstructor()->getMock();

        $this->getRequest()->setParam('address', []);

        $this->getRequest()->setParam('address', [
            'country' => 'NL',
            'postcode' => '1014 BA',
        ]);

        $this->_objectManager->configure([
            'preferences' => [
                \TIG\PostNL\Webservices\Endpoints\TimeFrame::class => get_class($timeFrame),
                \TIG\PostNL\Helper\AddressEnhancer::class => get_class($addressEnhancer),
                \TIG\PostNL\Webservices\Endpoints\DeliveryDate::class => get_class($deliveryDate),
                \TIG\PostNL\Webservices\Endpoints\Locations::class => get_class($location),
            ]
        ]);

        $deliveryDate = $this->_objectManager->get(DeliveryDate::class);
        $deliveryDate->method('call')->willReturn((object)['DeliveryDate' => '01-01-2001']);

        $timeFrame = $this->_objectManager->get(TimeFrame::class);
        $timeFrame->method('call')->willReturn(['timeframe1' => 'today', 'timeframe2' => 'tomorrow']);

        $location = $this->_objectManager->get(Locations::class);
        $location->method('call')->willReturn((object)[
            'GetLocationsResult' => (object)[
                'ResponseLocation' => [
                    'location1' => 'Amsterdam',
                    'location2' => 'Diemen',
                ]
            ]
        ]);
    }
}
