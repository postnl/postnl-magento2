<?php
declare(strict_types=1);

namespace TIG\PostNL\Test\Unit\Service\Customer;

use Magento\Customer\Helper\Address;
use TIG\PostNL\Service\Customer\Data;
use TIG\PostNL\Test\TestCase;

class DataTest extends TestCase
{
    private $addressHelperMock;
    private Data $data;

    protected function setUp(): void
    {
        $this->addressHelperMock = $this->createMock(Address::class);
        $this->data = new Data($this->addressHelperMock);

        parent::setUp();
    }

    public function testGetAddressLinesExtendCountReturnsInitialValue()
    {
        $this->assertSame(0, $this->data->getAddressLinesExtendCount());
    }

    public function testCanExtendAddressLinesReturnsTrueWhenAllowedLinesLessThanMax()
    {
        $this->addressHelperMock->method('getStreetLines')->willReturn(2);
        $this->assertTrue($this->data->canExtendAddressLines());
    }

    public function testCanExtendAddressLinesReturnsFalseWhenAllowedLinesEqualOrGreaterThanMax()
    {
        $this->addressHelperMock->method('getStreetLines')->willReturn(3);
        $this->assertFalse($this->data->canExtendAddressLines());

        $this->addressHelperMock->method('getStreetLines')->willReturn(4);
        $this->assertFalse($this->data->canExtendAddressLines());
    }

    public function testSetAddressLineExtendSetsValueWhenCanExtend()
    {
        $this->addressHelperMock->method('getStreetLines')->willReturn(2);
        $this->data->setAddressLineExtend();
        $this->assertSame(1, $this->data->getAddressLinesExtendCount());
    }

    public function testSetAddressLineExtendDoesNothingWhenCannotExtend()
    {
        $this->addressHelperMock->method('getStreetLines')->willReturn(3);
        $this->data->setAddressLineExtend();
        $this->assertSame(0, $this->data->getAddressLinesExtendCount());
    }
}
