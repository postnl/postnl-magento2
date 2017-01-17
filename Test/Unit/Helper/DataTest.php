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
namespace TIG\PostNL\Test\Unit\Helper;

use Magento\Sales\Model\Order;
use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Provider\ShippingOptions;

class DataTest extends TestCase
{
    protected $instanceClass = Helper::class;

    public function isPostNLOrderProvider()
    {
        return [
            ['tig_postnl_regular', true],
            ['dhl_regular', false],
        ];
    }

    /**
     * @param $shippingMethod
     * @param $expected
     *
     * @dataProvider isPostNLOrderProvider
     */
    public function testIsPostNLOrder($shippingMethod, $expected)
    {
        /** @var Order $order */
        $order = $this->objectManager->getObject(Order::class);
        $order->setData('shipping_method', $shippingMethod);

        /** @var bool $result */
        $result = $this->getInstance()->isPostNLOrder($order);

        $this->assertEquals($expected, $result);
    }

    public function testGetAllowedDeliveryOptions()
    {
        $result = $this->getInstance()->getAllowedDeliveryOptions();
        $this->assertTrue(is_array($result));
    }

    public function testGetAllowedDeliveryOptionsHasPakjeGemak()
    {
        $shippingOptionsConfigurationMock = $this->getFakeMock(ShippingOptions::class)->getMock();
        $isPakjeGemakActiveExpects = $shippingOptionsConfigurationMock->expects($this->once());
        $isPakjeGemakActiveExpects->method('isPakjegemakActive');
        $isPakjeGemakActiveExpects->willReturn(true);

        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptionsConfigurationMock
        ]);

        $result = $instance->getAllowedDeliveryOptions();
        $this->assertContains('PG', $result);
    }
}
