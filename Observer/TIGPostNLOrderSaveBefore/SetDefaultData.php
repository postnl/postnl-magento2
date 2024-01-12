<?php

namespace TIG\PostNL\Observer\TIGPostNLOrderSaveBefore;

use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Service\Order\ShipAt;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Order\FirstDeliveryDate;
use TIG\PostNL\Service\Options\ItemsToOption;
use TIG\PostNL\Service\Order\MagentoOrder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TIG\PostNL\Service\Quote\ShippingDuration;

// @codingStandardsIgnoreFile
class SetDefaultData implements ObserverInterface
{
    /**
     * @var ProductInfo
     */
    private $productInfo;

    /**
     * @var FirstDeliveryDate
     */
    private $firstDeliveryDate;

    /**
     * @var ShipAt
     */
    private $shipAt;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var ItemsToOption
     */
    private $itemsToOption;

    /**
     * @var MagentoOrder
     */
    private $magentoOrder;

    /**
     * @var ShippingDuration
     */
    private $shippingDuration;

    /**
     * @var array
     */
    private $shouldUpdateByOption = [
        ProductInfo::OPTION_EXTRAATHOME
    ];

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * SetDefaultData constructor.
     *
     * @param ProductInfo             $productInfo
     * @param FirstDeliveryDate       $firstDeliveryDate
     * @param ShipAt                  $shipAt
     * @param Log                     $log
     * @param ItemsToOption           $itemsToOption
     * @param MagentoOrder            $magentoOrder
     * @param ShippingDuration        $shippingDuration
     * @param CartRepositoryInterface $quoteRepository
     * @param Session                 $checkoutSession
     */
    public function __construct(
        ProductInfo $productInfo,
        FirstDeliveryDate $firstDeliveryDate,
        ShipAt $shipAt,
        Log $log,
        ItemsToOption $itemsToOption,
        MagentoOrder $magentoOrder,
        ShippingDuration $shippingDuration,
        CartRepositoryInterface $quoteRepository,
        Session $checkoutSession
    ) {
        $this->productInfo       = $productInfo;
        $this->firstDeliveryDate = $firstDeliveryDate;
        $this->shipAt            = $shipAt;
        $this->log               = $log;
        $this->itemsToOption     = $itemsToOption;
        $this->magentoOrder      = $magentoOrder;
        $this->shippingDuration  = $shippingDuration;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getData('data_object');

        try {
            $this->setData($order);
        } catch (\Exception $exception) {
            $this->log->critical($exception->getTraceAsString());
        }
    }

	/**
	 * @param \TIG\PostNL\Api\Data\OrderInterface $order
	 *
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
    private function setData(OrderInterface $order)
    {
        $option      = $this->getOptionFromQuote();
        $address     = $this->checkByAddressData($order);
        $type = '';
        // Check if code was already applied, if it wasn't - assume auto mode, so we can check for GP
        if (!$order->getProductCode()) {
            $type = ProductInfo::SHIPMENT_TYPE_AUTO;
        }
        $productInfo = $this->productInfo->get($type, $option, $address);
        $duration    = $this->shippingDuration->get();

        if (!$order->getProductCode() || $this->canUpdate($order->getProductCode(), $productInfo['code'], $option)) {
            $order->setProductCode($productInfo['code']);
        }

        if (!$order->getType() || $this->canUpdate($order->getType(), $productInfo['type'], $option)) {
            $order->setType($productInfo['type']);
        }

        if (!$order->getShippingDuration()) {
            $order->setShippingDuration($duration);
        }

        if (!$order->getDeliveryDate()) {
            $this->firstDeliveryDate->set($order);
        }

        // As long as the Magento Order is not saved the ship at is not determined.
        if (!$order->getShipAt() || !$order->getOrderId()) {
            $this->shipAt->set($order);
        }
    }

    /**
     * @param $current
     * @param $new
     * @param $option
     *
     * @return bool
     */
    private function canUpdate($current, $new, $option)
    {
        if (!in_array($option, $this->shouldUpdateByOption)) {
            return false;
        }

        if ($current == $new) {
            return false;
        }

        return true;
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOptionFromQuote()
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        if ($quoteId) {
            $quote = $this->quoteRepository->get($quoteId);

            return $this->itemsToOption->getFromQuote($quote);
        }

        return null;
    }

    /**
     * @param OrderInterface $order
     *
     * @return \Magento\Sales\Model\Order\Address|null
     */
    private function checkByAddressData(OrderInterface $order)
    {
        $address = null;
        $country = null;

        /** @noinspection PhpUndefinedMethodInspection */
        $address = $order->getPgAddress();
        if ($address) {
            return $address;
        }

        /**
         * Added try-catch wrapper to prevent 500-error in \Magento\Sales\Model\OrderRepository::get()
         * which occurred since Magento 2.2.8/2.3.1.
         */
        try {
            if ($order->getOrderId()) {
                return $this->magentoOrder->getShippingAddress($order->getOrderId());
            }
        } catch (\Error $e) {
            if ($order->getQuoteId()) {
                return $address = $this->magentoOrder->getShippingAddress($order->getQuoteId(), 'quote');
            }
        }

        if ($order->getQuoteId()) {
            return $this->magentoOrder->getShippingAddress($order->getQuoteId(), 'quote');
        }

        return null;
    }
}
