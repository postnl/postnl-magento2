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

namespace TIG\PostNL\Unit\Service\Timeframe\Filters\Options;

use Exception;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Timeframe\Filters\Options\Evening;
use TIG\PostNL\Config\Provider\ShippingOptions;

class EveningTest extends TestCase
{
    protected $instanceClass = Evening::class;

    /**
     * @dataProvider TIG\PostNL\Test\Fixtures\Timeframes\Options\DataProvider::evening
     *
     * @param $isEnabled
     * @param $options
     * @param $expected
     *
     * @throws Exception
     */
    public function testFilter($isEnabled, $options, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $expects = $shippingOptions->expects($this->once());
        $expects->method('isEveningDeliveryActive');
        $expects->willReturn($isEnabled);

        $instance = $this->getInstance(['shippingOptions' => $shippingOptions]);
        $result   = $instance->filter($options);

        $this->assertEquals($expected, $result);
    }
}
