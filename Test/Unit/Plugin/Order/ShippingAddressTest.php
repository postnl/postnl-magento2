<?php

namespace TIG\PostNL\Test\Unit\Plugin\Order;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Plugin\Order\ShippingAddress;
use TIG\PostNL\Test\TestCase;

class ShippingAddressTest extends TestCase
{
    public $instanceClass = ShippingAddress::class;

    public function testDoesNotLookUpThePostNLOrderForOtherShippingMethods()
    {
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->expects($this->never())->method('getByOrderId');
        $homeAddress = $this->getAddress(1);

        $result = $this->getInstance(['orderRepository' => $orderRepository])
            ->afterGetShippingAddress($this->getOrder('flatrate_flatrate', [$homeAddress]), $homeAddress);

        $this->assertSame($homeAddress, $result);
    }

    public function testReturnsThePickupPointFromTheLoadedAddresses()
    {
        $homeAddress = $this->getAddress(1);
        $pickupPoint = $this->getAddress(2);
        $order = $this->getOrder('tig_postnl_regular', [$homeAddress, $pickupPoint], true);
        $order->expects($this->never())->method('getAddressById');

        $result = $this->getInstance(['orderRepository' => $this->getOrderRepository(2)])
            ->afterGetShippingAddress($order, $homeAddress);

        $this->assertSame($pickupPoint, $result);
    }

    public function testLoadsThePickupPointWhenTheOrderHasNotLoadedIt()
    {
        $homeAddress = $this->getAddress(1);
        $pickupPoint = $this->getAddress(2);
        $order = $this->getOrder('tig_postnl_regular', [$homeAddress], true);
        $order->expects($this->once())->method('getAddressById')->with(2)->willReturn($pickupPoint);

        $result = $this->getInstance(['orderRepository' => $this->getOrderRepository(2)])
            ->afterGetShippingAddress($order, $homeAddress);

        $this->assertSame($pickupPoint, $result);
    }

    public function testKeepsTheShippingAddressWithoutPickupPoint()
    {
        $homeAddress = $this->getAddress(1);

        $result = $this->getInstance(['orderRepository' => $this->getOrderRepository(null)])
            ->afterGetShippingAddress($this->getOrder('tig_postnl_regular', [$homeAddress]), $homeAddress);

        $this->assertSame($homeAddress, $result);
    }

    /**
     * @param string $shippingMethod
     * @param Address[] $addresses
     * @param bool $withExpectations
     *
     * @return Order|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\Stub
     */
    private function getOrder($shippingMethod, array $addresses, $withExpectations = false)
    {
        $order = $withExpectations ? $this->createMock(Order::class) : $this->createStub(Order::class);
        $order->method('getId')->willReturn(10);
        $order->method('getShippingMethod')->willReturn($shippingMethod);
        $order->method('getAddresses')->willReturn($addresses);

        return $order;
    }

    /**
     * @param int $id
     *
     * @return Address|\PHPUnit\Framework\MockObject\Stub
     */
    private function getAddress($id)
    {
        $address = $this->createStub(Address::class);
        $address->method('getId')->willReturn($id);

        return $address;
    }

    /**
     * @param int|null $pgOrderAddressId
     *
     * @return OrderRepository|\PHPUnit\Framework\MockObject\Stub
     */
    private function getOrderRepository($pgOrderAddressId)
    {
        $postnlOrder = $this->createStub(PostNLOrder::class);
        $postnlOrder->method('getPgOrderAddressId')->willReturn($pgOrderAddressId);

        $orderRepository = $this->createStub(OrderRepository::class);
        $orderRepository->method('getByOrderId')->willReturn($postnlOrder);

        return $orderRepository;
    }
}
