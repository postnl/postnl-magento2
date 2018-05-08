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
namespace TIG\PostNL\Test\Integration\Service\Quote;

use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Quote\Model\Quote;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Product\CollectionByItems;

class ShippingDurationTest extends TestCase
{
    public $instanceClass = ShippingDuration::class;

    public function getDataProvider()
    {
        return  [
            'has quote' => [true, '3'], // Product fixture set on '3'.
            'has no quote' => [false, '1'] // Default system configuration fallback.
        ];
    }

    /**
     * @param $hasQuote
     * @param $expected
     *
     * @dataProvider getDataProvider
     */
    public function testGet($hasQuote, $expected)
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteShippingDuration.php';
        $quote = $this->getQuote($hasQuote);

        $checkoutSession        = $this->getFakeMock(QuoteInterface::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quote);

        $webshopConfiguration = $this->getFakeMock(Webshop::class)->getMock();
        $webshopExpects = $webshopConfiguration->expects($this->any())->method('getShippingDuration');
        $webshopExpects->willReturn('1');

        $instance = $this->getInstance([
            'checkoutSession' => $checkoutSession,
            'webshopConfiguration' => $webshopConfiguration
        ]);

        $restult = $instance->get();
        $this->assertEquals($expected, $restult);
    }

    /**
     * @param $hasQuote
     * @return Quote
     */
    private function getQuote($hasQuote)
    {
        if (!$hasQuote) {
            return null;
        }

        return $this->getObject(Quote::class)->load('shippingDuration_01', 'reserved_order_id');
    }
}