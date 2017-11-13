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

namespace TIG\PostNL\Test\Unit\Service\Validation;

class ParcelTypeTest extends Contract
{
    public $instanceClass = \TIG\PostNL\Service\Validation\ParcelType::class;

    public function shouldAllowDifferentVariationsOfTheParcelTypeProvider()
    {
        return [
            'regular'                               => ['regular', 'regular'],
            'regular with a capital'                => ['Regular', 'regular'],
            'extra@home regular'                    => ['extra@home', 'extra@home'],
            'extra@home with spaces'                => ['extra @ home', 'extra@home'],
            'extra@home with underscores'           => ['extra_@_home', 'extra@home'],
            'extra@home with an at'                 => ['extraathome', 'extra@home'],
            'extra@home with an at and underscores' => ['extra_at_home', 'extra@home'],
            'pakjegemak variation 1'                => ['pakjegemak', 'pakjegemak'],
            'pakjegemak variation 2'                => ['pakje_gemak', 'pakjegemak'],
            'pakjegemak variation 3'                => ['pakje gemak', 'pakjegemak'],
            'pakjegemak variation 4'                => ['PakjeGemak', 'pakjegemak'],
            'pakjegemak variation 5'                => ['postkantoor', 'pakjegemak'],
            'pakjegemak variation 6'                => ['post office', 'pakjegemak'],
        ];
    }

    /**
     * @dataProvider shouldAllowDifferentVariationsOfTheParcelTypeProvider
     *
     * @param $parcelType
     * @param $expected
     */
    public function testShouldAllowDifferentVariationsOfTheParcelType($parcelType, $expected)
    {
        $this->assertEquals($expected, $this->getInstance()->validate($parcelType));
    }

    public function testInvalidParcelTypeShouldReturnFalse()
    {
        $this->assertFalse($this->getInstance()->validate('invalid'));
    }

    public function emptyParcelTypeShouldDefaultToStar()
    {
        return [
            [''],
            ['0'],
            ['*'],
        ];
    }

    /**
     * @dataProvider emptyParcelTypeShouldDefaultToStar
     */
    public function testEmptyParcelTypeShouldDefaultToStar($value)
    {
        $this->assertSame('*', $this->getInstance()->validate($value));
    }
}
