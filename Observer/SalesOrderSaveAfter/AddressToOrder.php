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
namespace TIG\PostNL\Observer\SalesOrderSaveAfter;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Model\Order\AddressFactory;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use TIG\PostNL\Model\OrderRepository;

class AddressToOrder implements ObserverInterface
{
    /**
     * @var ToOrderAddress
     */
    private $quoteAddressToOrderAddress;

    /**
     * @var PickupAddress
     */
    private $pickupAddressHelper;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param ToOrderAddress          $toOrderAddress
     * @param PickupAddress           $pickupAddress
     * @param AddressFactory          $addressFactory
     * @param OrderRepository         $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        ToOrderAddress $toOrderAddress,
        PickupAddress $pickupAddress,
        AddressFactory $addressFactory,
        OrderRepository $orderRepository,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteAddressToOrderAddress = $toOrderAddress;
        $this->pickupAddressHelper = $pickupAddress;
        $this->addressFactory = $addressFactory;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order         = $observer->getData('order');
        $postnlOrder   = $this->getPostNLOrder($order);

        $this->addPgAddress($order, $postnlOrder);

        return $this;
    }

    /**
     * @param $order
     * @param $postnlOrder
     *
     * @throws Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addPgAddress($order, $postnlOrder)
    {
        $pgAddress      = false;
        $quotePgAddress = $this->pickupAddressHelper->getPakjeGemakAddressInQuote($order->getQuoteId());

        if ($quotePgAddress->getTelephone() === null) {
            $billingAddress = $order->getBillingAddress();
            $quotePgAddress->setTelephone($billingAddress->getTelephone());
        }

        if ($quotePgAddress->getId() && $this->shouldAdd($order, $postnlOrder)) {
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $quotePgAddress->setQuote($quote);

            /** @var Order\Address $orderPgAddress */
            $orderPgAddress = $this->quoteAddressToOrderAddress->convert($quotePgAddress);
            $pgAddress      = $this->createOrderAddress($orderPgAddress, $order);
            $order->addAddress($pgAddress);
        }

        if (false !== $pgAddress && $postnlOrder->getId()) {
            $postnlOrder->setData('pg_order_address_id', $pgAddress->getId());
            $this->orderRepository->save($postnlOrder);
        }
    }

    /**
     * @param $order
     * @param $postnlOrder
     *
     * @return bool
     */
    private function shouldAdd($order, $postnlOrder)
    {
        if (!$this->isPostNLShipment($order)) {
            return false;
        }

        if ($this->addressAlreadyAdded($order, $postnlOrder)) {
            return false;
        }

        return true;
    }

    /**
     * @param Order                   $order
     * @param \TIG\PostNL\Model\Order $postnlOrder
     *
     * @return bool
     */
    private function addressAlreadyAdded(Order $order, \TIG\PostNL\Model\Order $postnlOrder)
    {
        if (!$postnlOrder->getPgOrderAddressId()) {
            return false;
        }

        if ($order->getAddressById($postnlOrder->getPgOrderAddressId())) {
            return true;
        }

        return false;
    }

    /**
     * @param Order $order
     *
     * @return null|\TIG\PostNL\Model\Order
     */
    private function getPostNLOrder(Order $order)
    {
        $postnlOrder = $this->orderRepository->getByOrderId($order->getId());
        if (!$postnlOrder) {
            $postnlOrder = $this->orderRepository->getByQuoteWhereOrderIdIsNull($order->getQuoteId());
        }

        if (!$postnlOrder) {
            return null;
        }

        if ($postnlOrder->getOrderId() == null) {
            return $postnlOrder;
        }

        if ($order->getId() == $postnlOrder->getOrderId()) {
            return $postnlOrder;
        }

        return $this->orderRepository->create();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    private function isPostNLShipment($order)
    {
        $method = $order->getShippingMethod();
        return $method === 'tig_postnl_regular';
    }

    /**
     * @param Order\Address $orderPgAddress
     * @param Order $order
     *
     * @return Order\Address
     * @throws Exception
     */
    private function createOrderAddress($orderPgAddress, $order)
    {
        $pgAddress = $this->addressFactory->create();
        $pgAddress->setParentId($order->getEntityId());
        $pgAddress->setCompany($orderPgAddress->getCompany());
        $pgAddress->setStreet($orderPgAddress->getStreet());
        $pgAddress->setCity($orderPgAddress->getCity());
        $pgAddress->setPostcode($orderPgAddress->getPostcode());
        $pgAddress->setCountryId($orderPgAddress->getCountryId());
        $pgAddress->setFirstname($orderPgAddress->getFirstname());
        $pgAddress->setLastname($orderPgAddress->getLastname());
        $pgAddress->setEmail($order->getCustomerEmail());
        $pgAddress->setTelephone($orderPgAddress->getTelephone());
        $pgAddress->setAddressType('shipping');
        $pgAddress->save();

        return $pgAddress;
    }
}
