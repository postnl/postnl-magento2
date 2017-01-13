<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Sales\Model\Order;
use \Magento\Quote\Model\Quote;
use \Magento\Quote\Model\Quote\Address\ToOrderAddress;
use \Magento\Sales\Model\Order\AddressFactory;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Model\OrderFactory;
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
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param ToOrderAddress  $toOrderAddress
     * @param PickupAddress   $pickupAddress
     * @param AddressFactory  $addressFactory
     * @param OrderFactory    $orderFactory
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        ToOrderAddress $toOrderAddress,
        PickupAddress $pickupAddress,
        AddressFactory $addressFactory,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository
    ) {
        $this->quoteAddressToOrderAddress = $toOrderAddress;
        $this->pickupAddressHelper = $pickupAddress;
        $this->addressFactory = $addressFactory;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('data_object');
        $quotePgAddres = $this->pickupAddressHelper->getPakjeGemakAddressInQuote($order->getQuoteId());
        $pgAddress     = false;
        if ($quotePgAddres->getId()) {
            /** @var Order\Address $orderPgAddress */
            $orderPgAddress = $this->quoteAddressToOrderAddress->convert($quotePgAddres);
            $pgAddress      = $this->createOrderAddress($orderPgAddress, $order);
            $order->addAddress($pgAddress);
        }

        $postnlOrder = $this->getPostNLOrder($order);
        if (false !== $pgAddress && $postnlOrder->getId()) {
            $postnlOrder->setData('pg_order_address_id', $pgAddress->getId());
            $this->orderRepository->save($postnlOrder);
        }

        return $this;
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

    /**
     * @param Order $magentoOrder
     *
     * @return PostNLOrder
     */
    private function getPostNLOrder(Order $magentoOrder)
    {
        /** @var PostNLOrder $postnlOrder */
        $postnlOrder = $this->orderFactory->create();

        /** @var \TIG\PostNL\Model\ResourceModel\Order\Collection $collection */
        $collection = $postnlOrder->getCollection();
        $collection->addFieldToFilter('quote_id', $magentoOrder->getQuoteId());

        // @codingStandardsIgnoreLine
        $postnlOrder = $collection->setPageSize(1)->getFirstItem();

        return $postnlOrder;
    }
}
