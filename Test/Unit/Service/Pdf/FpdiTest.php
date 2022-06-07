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
namespace TIG\PostNL\Test\Unit\Service\Pdf;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Test\TestCase;

class FpdiTest extends TestCase
{
    public $instanceClass = Fpdi::class;

    /**
     * @return array
     */
    public function pixelsToPointsProvider()
    {
        return [
            'no pixels' => [0, 0],
            'single digit pixel' => [3, 0.8],
            'multi digit pixel' => [123, 32.4],
            'single decimal pixel' => [45.6, 12],
            'multi decimal pixel' => [78.901, 20.8]
        ];
    }

    /**
     * @param $pixels
     * @param $expected
     *
     * @dataProvider pixelsToPointsProvider
     */
    public function testPixelsToPoints($pixels, $expected)
    {
        $instance = $this->getInstance();
        $result = $instance->pixelsToPoints($pixels);
        $this->assertEquals($result, $expected);
    }
}
