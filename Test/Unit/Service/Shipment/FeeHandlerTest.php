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
namespace TIG\PostNL\Test\Unit\Service\Shipment;

use TIG\PostNL\Service\Shipment\FeeHandler;
use TIG\PostNL\Test\TestCase;

class FeeHandlerTest extends TestCase
{
    public $instanceClass = FeeHandler::class;

    public function testShouldNotSaveWhenThereIsNoFee()
    {
        $checkoutSession = $this->getCheckoutSessionMock([
            ['key' => 'tig_postnl_regular_base_amount', 'value' => null],
            ['key' => 'tig_postnl_regular_fee_amount', 'value' => null],
        ]);

        $quote = $this->getQuote();
        $quote->expects($this->never())->method('getShippingAddress');

        /** @var FeeHandler $instance */
        $instance = $this->getInstance([
            'quote' => $quote,
            'checkoutSession' => $checkoutSession,
        ]);

        $instance->save();
    }

    public function testShouldSaveWhenThereIsAFee()
    {
        $checkoutSession = $this->getCheckoutSessionMock([
            ['key' => 'tig_postnl_regular_base_amount', 'value' => '5.00'],
            ['key' => 'tig_postnl_regular_fee_amount', 'value' => '2.00'],
        ]);

        $quote = $this->getQuote();
        $address = $this->getShippingAddress($quote);
        $address->expects($this->once())->method('setShippingAmount')->with('7.00');

        /** @var FeeHandler $instance */
        $instance = $this->getInstance([
            'quoteWrapper' => $quote,
            'checkoutSession' => $checkoutSession,
        ]);

        $instance->save();
    }

    private function getCheckoutSessionMock(array $input)
    {
        $checkoutSession = $this->getMock(\TIG\PostNL\Service\Wrapper\CheckoutSessionInterface::class);

        foreach ($input as $index => $data) {
            $expects = $checkoutSession->expects($this->at($index));
            $expects->method('getValue');
            $expects->with($data['key']);
            $expects->willReturn($data['value']);
        }

        return $checkoutSession;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getQuote()
    {
        $quote = $this->getMock(\TIG\PostNL\Service\Wrapper\QuoteInterface::class);

        return $quote;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $quote
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getShippingAddress(\PHPUnit_Framework_MockObject_MockObject $quote)
    {
        $address = $this->getMock(\stdClass::class, ['setShippingAmount']);

        $quote->expects($this->once())->method('getShippingAddress')->willReturn($address);

        return $address;
    }
}
