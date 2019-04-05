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
namespace TIG\PostNL\Observer\TIGPostNLOrderSaveBefore;

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Service\Order\ShipAt;
use TIG\PostNL\Service\Order\ProductCodeAndType;
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
     * @var ProductCodeAndType
     */
    private $productCodeAndType;

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
        ProductCodeAndType::OPTION_EXTRAATHOME
    ];

    /**
     * SetDefaultData constructor.
     *
     * @param ProductCodeAndType $productCodeAndType
     * @param FirstDeliveryDate  $firstDeliveryDate
     * @param ShipAt             $shipAt
     * @param Log                $log
     * @param ItemsToOption      $itemsToOption
     * @param MagentoOrder       $magentoOrder
     * @param ShippingDuration   $shippingDuration
     */
    public function __construct(
        ProductCodeAndType $productCodeAndType,
        FirstDeliveryDate $firstDeliveryDate,
        ShipAt $shipAt,
        Log $log,
        ItemsToOption $itemsToOption,
        MagentoOrder $magentoOrder,
        ShippingDuration $shippingDuration
    ) {
        $this->productCodeAndType = $productCodeAndType;
        $this->firstDeliveryDate  = $firstDeliveryDate;
        $this->shipAt             = $shipAt;
        $this->log                = $log;
        $this->itemsToOption      = $itemsToOption;
        $this->magentoOrder       = $magentoOrder;
        $this->shippingDuration   = $shippingDuration;
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
     * @param $order
     */
    private function setData(OrderInterface $order)
    {
        $option      = $this->getOptionFromQuote();
        $address     = $this->checkByAddressData($order);
        $productInfo = $this->productCodeAndType->get('', $option, $address);
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
     * @return string
     */
    private function getOptionFromQuote()
    {
        return $this->itemsToOption->getFromQuote();
    }

    /**
     * @param OrderInterface $order
     *
     * @return \Magento\Sales\Model\Order\Address|null
     */
    private function checkByAddressData(OrderInterface $order)
    {
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
