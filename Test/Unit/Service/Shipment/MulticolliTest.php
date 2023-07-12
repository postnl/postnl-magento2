<?php

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
