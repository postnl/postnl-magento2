<?php

namespace TIG\PostNL\Test\Unit\Webservices\Api;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Api\Customer;

class CustomerTest extends TestCase
{
    protected $instanceClass = Customer::class;

    public function getProvider()
    {
        return [
            ['a', 'b', ['CustomerCode' => 'a', 'CustomerNumber' => 'b']],
            ['c', 'd', ['CustomerCode' => 'c', 'CustomerNumber' => 'd']],
        ];
    }

    /**
     * @param $customerCode
     * @param $customerNumber
     * @param $expected
     *
     * @dataProvider getProvider
     */
    public function testGet($customerCode, $customerNumber, $expected)
    {
        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class)->getMock();

        $getCustomerCodeExpects = $accountConfigurationMock->expects($this->once());
        $getCustomerCodeExpects->method('getCustomerCode');
        $getCustomerCodeExpects->willReturn($customerCode);

        $getCustomerCodeExpects = $accountConfigurationMock->expects($this->once());
        $getCustomerCodeExpects->method('getCustomerNumber');
        $getCustomerCodeExpects->willReturn($customerNumber);

        $instance = $this->getInstance([
            'accountConfiguration' => $accountConfigurationMock,
        ]);

        $result = $instance->get();

        $this->assertEquals($expected, $result);
    }
}
