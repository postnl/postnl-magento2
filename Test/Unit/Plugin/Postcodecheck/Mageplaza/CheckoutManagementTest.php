<?php

namespace TIG\PostNL\Test\Unit\Plugin\Postcodecheck\Mageplaza;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Plugin\Postcodecheck\Mageplaza\CheckoutManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use \Magento\Quote\Api\Data\AddressInterface;

class CheckoutManagementTest extends TestCase
{
    protected $instanceClass = CheckoutManagement::class;


    public function testBeforeSaveCheckoutInformation()
    {
        $customAttributes = ['tig_housenumber' => '37'];

        $instance = $this->getInstance();

        $addressInformation = $this->getAddressMock(['Kabelweg', 37]);

        $expected = [1, $addressInformation, $customAttributes, []];
        $result = $instance->beforeSaveCheckoutInformation(null, 1, $addressInformation, $customAttributes);

        $this->assertEquals($expected, $result);
    }

    public function testBeforeSaveCheckoutInformationWithoutTigHousenumber()
    {
        $instance = $this->getInstance();
        $addressInformation = $this->getAddressMock('Kabwelweg');

        $expected = [1, $addressInformation, ['test' => 'test'], []];
        $result = $instance->beforeSaveCheckoutInformation(null, 1, $addressInformation, ['test' => 'test']);

        $this->assertEquals($expected, $result);
    }

    public function testBeforeSaveCheckoutInformationWithEmptyCustomAttributes()
    {
        $instance = $this->getInstance();
        $addressInformation = $this->getAddressMock('Kabwelweg');

        $expected = [1, $addressInformation, [], []];
        $result = $instance->beforeSaveCheckoutInformation(null, 1, $addressInformation);

        $this->assertEquals($expected, $result);
    }

    private function getAddressMock($street)
    {
        $addressMock = $this->getFakeMock(AddressInterface::class)->getMock();
        $addressExpects = $addressMock->expects($this->any());
        $addressExpects->method('getStreet')->willReturn('Kabelweg');
        $addressExpects2 = $addressMock->expects($this->any());
        $addressExpects2->method('setStreet')->with($street);

        $shippingAddressMock = $this->getFakeMock(ShippingInformationInterface::class)->getMock();
        $shippingAddressMockExpects = $shippingAddressMock->expects($this->any())->method('getShippingAddress');
        $shippingAddressMockExpects->willReturn($addressMock);
        $shippingAddressMockExpects2 = $shippingAddressMock->expects($this->any())->method('setShippingAddress');

        return $shippingAddressMock;
    }
}