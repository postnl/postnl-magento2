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
use TIG\PostNL\Service\Order\ProductCode;
use TIG\PostNL\Service\Order\FirstDeliveryDate;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetDefaultData implements ObserverInterface
{
    /**
     * @var ProductCode
     */
    private $productCode;

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
     * SetDefaultData constructor.
     *
     * @param ProductCode       $productCode
     * @param FirstDeliveryDate $firstDeliveryDate
     * @param ShipAt            $shipAt
     * @param Log               $log
     */
    public function __construct(
        ProductCode $productCode,
        FirstDeliveryDate $firstDeliveryDate,
        ShipAt $shipAt,
        Log $log
    ) {
        $this->productCode = $productCode;
        $this->firstDeliveryDate = $firstDeliveryDate;
        $this->shipAt = $shipAt;
        $this->log = $log;
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
        if (!$order->getProductCode()) {
            $order->setProductCode($this->productCode->get());
        }

        if (!$order->getDeliveryDate()) {
            $this->firstDeliveryDate->set($order);
        }

        if (!$order->getShipAt()) {
            $this->shipAt->set($order);
        }
    }
}
