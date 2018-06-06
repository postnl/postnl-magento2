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

use TIG\PostNL\Config\Source\Settings\CutOffSettings;
use TIG\PostNL\Test\TestCase;

class CutOffSettingsTest extends TestCase
{
    public $instanceClass = CutOffSettings::class;

    public function testToOptionArray()
    {
        /** @var CutOffSettings $instance */
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertEquals('00:00', $result[0]['label']->render());

        // 24 hours x 4 = 96;
        $this->assertCount(96, $result);
        $this->assertEquals('00:00:00', $result[0]['value']);
        $this->assertEquals('00:15:00', $result[1]['value']);
        $this->assertEquals('00:30:00', $result[2]['value']);
        $this->assertEquals('00:45:00', $result[3]['value']);
        $this->assertEquals('23:45:00', end($result)['value']);
    }
}
