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
namespace TIG\PostNL\Test\Unit\Service\Shipment;

use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Service\Shipment\Multicolli;
use TIG\PostNL\Test\TestCase;

class MulticolliTest extends TestCase
{
    public $instanceClass = Multicolli::class;

    public function ifTheRightCountryIsAllowedToUseMulticolliProvider()
    {
        return [
            'nl => nl' => ['NL', 'NL', true],
            'nl => be' => ['NL', 'BE', true],
            'be => nl' => ['BE', 'NL', false],
            'be => be' => ['BE', 'BE', false],
            'nl => de' => ['NL', 'DE', false],
            'nl => fr' => ['NL', 'FR', false],
            'nl => us' => ['NL', 'US', false],
            'nl => cn' => ['NL', 'CN', false],
        ];
    }

    /**
     * @dataProvider ifTheRightCountryIsAllowedToUseMulticolliProvider
     *
     * @param $from
     * @param $to
     * @param $expected
     */
    public function testIfTheRightCountryIsAllowedToUseMulticolli($from, $to, $expected)
    {
        $configMock = $this->getFakeMock(AddressConfiguration::class, true);

        $getCountry = $configMock->method('getCountry');
        $getCountry->willReturn($from);

        $result = $this->getInstance(['addressConfiguration' => $configMock])->get($to);

        $this->assertEquals($expected, $result);
        $this->assertSame($expected, $result);
    }
}
