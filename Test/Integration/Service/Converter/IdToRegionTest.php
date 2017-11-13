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

namespace TIG\PostNL\Test\Integration\Service\Converter;

class IdToRegionTest extends Contract
{
    public $instanceClass = \TIG\PostNL\Service\Converter\IdToRegion::class;

    public function testItConvertsZeroToStar()
    {
        $this->assertEquals('*', $this->getInstance()->convert(0));
    }

    public function testItConvertsAnIdToAName()
    {
        $this->assertEquals('California', $this->getInstance()->convert(12));
        $this->assertEquals('Colorado', $this->getInstance()->convert(13));
    }

    public function testThrowsAnExceptionWhenTheRegionIsInvalid()
    {
        try {
            $this->getInstance()->convert('non existing');
        } catch (\TIG\PostNL\Exception $exception) {
            $this->assertEquals('"non existing" is not a valid region', $exception->getMessage());
            return;
        }

        $this->fail('We expected an \TIG\PostNL\Exception but got none');
    }
}
