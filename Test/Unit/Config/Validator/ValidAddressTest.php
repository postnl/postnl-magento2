<?php

namespace TIG\PostNL\Test\Unit\Config\Validator;

use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Validator\ValidAddress;
use TIG\PostNL\Test\TestCase;

class ValidAddressTest extends TestCase
{
    public $instanceClass = ValidAddress::class;

    /**
     * @var AddressConfiguration|\PHPUnit\Framework\MockObject\MockObject
     */
    private $addressMock;

    /**
     * @var ValidAddress
     */
    private $instance;

    public function setUp() : void
    {
        parent::setUp();

        $addressMock = $this->getFakeMock(AddressConfiguration::class);
        $addressMock->setMethods(['getAddressInfo']);
        $this->addressMock = $addressMock->getMock();
    }

    public function getInstance(array $args = array())
    {
        $args['addressConfiguration'] = $this->addressMock;

        return parent::getInstance($args);
    }

    public function hasValidNameProvider()
    {
        return [
            'no_data' => [
                ['firstname' => '', 'lastname' => '', 'company' => ''],
                false
            ],
            'valid firstname' => [
                ['firstname' => 'valid', 'lastname' => '', 'company' => ''],
                false
            ],
            'valid lastname' => [
                ['firstname' => '', 'lastname' => 'valid', 'company' => ''],
                false
            ],
            'valid name' => [
                ['firstname' => 'valid', 'lastname' => 'valid', 'company' => ''],
                true
            ],
            'valid company' => [
                ['firstname' => '', 'lastname' => '', 'company' => 'valid'],
                true
            ],
            'valid name and company' => [
                ['firstname' => 'valid', 'lastname' => 'valid', 'company' => 'valid'],
                true
            ],
        ];
    }

    /**
     * @param $data
     * @param $expected
     *
     * @dataProvider hasValidNameProvider
     */
    public function testHasValidName($data, $expected)
    {
        $this->setAddressInfo($data);

        $result = $this->invoke('hasValidName', $this->getInstance());

        $this->assertSame($expected, $result);
    }

    private function setAddressInfo($data)
    {
        $getAddressInfoExpects = $this->addressMock->expects($this->once());
        $getAddressInfoExpects->method('getAddressInfo');
        $getAddressInfoExpects->willReturn($data);
    }

    public function checkProvider()
    {
        return [
            'no data' => [
                [
                    'firstname'            => '',
                    'lastname'             => '',
                    'company'              => '',
                    'street'               => '',
                    'department'           => '',
                    'housenumber'          => '',
                    'housenumber_addition' => '',
                    'postcode'             => '',
                    'city'                 => '',
                ],
                false,
            ],
            'valid name' => [
                [
                    'firstname'            => 'valid',
                    'lastname'             => 'valid',
                    'company'              => 'valid',
                    'street'               => '',
                    'department'           => '',
                    'housenumber'          => '',
                    'housenumber_addition' => '',
                    'postcode'             => '',
                    'city'                 => '',
                ],
                false,
            ],
            'valid street' => [
                [
                    'firstname'            => 'valid',
                    'lastname'             => 'valid',
                    'company'              => 'valid',
                    'street'               => 'valid',
                    'department'           => '',
                    'housenumber'          => '',
                    'housenumber_addition' => '',
                    'postcode'             => '',
                    'city'                 => '',
                ],
                false,
            ],
            'valid housenumber' => [
                [
                    'firstname'            => 'valid',
                    'lastname'             => 'valid',
                    'company'              => 'valid',
                    'street'               => 'valid',
                    'department'           => '',
                    'housenumber'          => 'valid',
                    'housenumber_addition' => '',
                    'postcode'             => '',
                    'city'                 => '',
                ],
                false,
            ],
            'valid postcode' => [
                [
                    'firstname'            => 'valid',
                    'lastname'             => 'valid',
                    'company'              => 'valid',
                    'street'               => 'valid',
                    'department'           => '',
                    'housenumber'          => 'valid',
                    'housenumber_addition' => '',
                    'postcode'             => 'valid',
                    'city'                 => '',
                ],
                false,
            ],
            'all valid' => [
                [
                    'firstname'            => 'valid',
                    'lastname'             => 'valid',
                    'company'              => 'valid',
                    'street'               => 'valid',
                    'department'           => '',
                    'housenumber'          => 'valid',
                    'housenumber_addition' => '',
                    'postcode'             => 'valid',
                    'city'                 => 'valid',
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider checkProvider
     */
    public function testCheck($data, $expected)
    {
        $this->setAddressInfo($data);

        $result = $this->getInstance()->check();

        $this->assertSame($expected, $result);
    }
}
