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
namespace TIG\PostNL\Test\Unit\Service\Timeframe\Filters\Days;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Service\Timeframe\Filters\Days\Sunday;
use TIG\PostNL\Test\TestCase;

class SundayTest extends TestCase
{
    public $instanceClass = Sunday::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::noFiltering
     *
     * @param $input
     * @param $output
     */
    public function testSundayIsEnabled($input, $output)
    {
        $this->assertEquals($output, $this->createInstance(true)->filter($input));
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Days\DataProvider::sundayDisabled
     *
     * @param $input
     * @param $output
     */
    public function testSundayIsDisabled($input, $output)
    {
        $this->assertEquals($output, $this->createInstance(false)->filter($input));
    }

    /**
     * @param $sundayEnabled
     *
     * @return Sunday
     */
    private function createInstance($sundayEnabled)
    {
        $mock = $this->getFakeMock(ShippingOptions::class, true);
        $this->mockFunction($mock, 'isSundayDeliveryActive', $sundayEnabled);

        $helper = $this->getObject(Data::class);

        $instance = $this->getInstance([
            'shippingOptions' => $mock,
            'helper' => $helper,
        ]);

        return $instance;
    }
}
