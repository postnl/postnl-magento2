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