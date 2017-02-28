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
namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Config\Source\Settings\DaysOfWeek;
use TIG\PostNL\Test\TestCase;

class DaysOfWeekTest extends TestCase
{
    public $instanceClass = DaysOfWeek::class;

    public function testToOptionArray()
    {
        /** @var DaysOfWeek $instance */
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertCount(7, $result);
        $this->assertEquals('1', $result[0]['value']);
        $this->assertEquals('Monday', $result[0]['label']->render());
        $this->assertEquals('0', $result[6]['value']);
        $this->assertEquals('Sunday', $result[6]['label']->render());
    }
}
