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
namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\CheckoutConfiguration\AbstractCheckoutConfiguration;
use TIG\PostNL\Test\TestCase;
use \TIG\PostNL\Config\Provider\CheckoutConfiguration;

class CheckoutConfigurationTest extends TestCase
{
    public $instanceClass = CheckoutConfiguration::class;

    public function getConfigProvider()
    {
        return [
            [
                'input' => [
                    'key1' => 'test1',
                    'else' => 'random',
                ],
                'expected' => [
                    'shipping' => [
                        'postnl' => [
                            'key1' => 'test1',
                            'else' => 'random',
                        ]
                    ]
                ]
            ],

            [
                'input' => [
                    'key1' => 'output1',
                    'key2' => 'output2',
                    'key3' => 'output3',
                ],
                'expected' => [
                    'shipping' => [
                        'postnl' => [
                            'key1' => 'output1',
                            'key2' => 'output2',
                            'key3' => 'output3',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param $shippingConfigurationInput
     * @param $expected
     *
     * @dataProvider getConfigProvider
     */
    public function testGetConfig($shippingConfigurationInput, $expected)
    {
        $shippingConfiguration = [];
        foreach ($shippingConfigurationInput as $key => $value) {
            $mock = $this->getMockForAbstractClass(AbstractCheckoutConfiguration::class);
            $mock->expects($this->once())->method('getValue')->willReturn($value);

            $shippingConfiguration[$key] = $mock;
        }

        /** @var CheckoutConfiguration $instance */
        $instance = $this->getInstance([
            'shippingConfiguration' => $shippingConfiguration,
        ]);

        $result = $instance->getConfig();

        $this->assertEquals($expected, $result);
    }
}
