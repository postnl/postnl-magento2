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
namespace TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\GridInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Model\Shipment;

class UpdateOrderGrid implements ObserverInterface
{
    /**
     * @var GridInterface
     */
    private $orderGrid;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param GridInterface            $orderGrid
     */
    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        GridInterface $orderGrid
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderGrid = $orderGrid;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getData('data_object');

        $this->updateOrder($shipment);
        $this->orderGrid->refresh($shipment->getOrderId());
    }

    /**
     * @param Shipment $shipment
     */
    private function updateOrder(Shipment $shipment)
    {
        /** @var \TIG\PostNL\Model\Order $order */
        $order = $this->orderRepositoryInterface->getByFieldWithValue('order_id', $shipment->getOrderId());

        if (!$order) {
            return;
        }

        $productCode = $shipment->getProductCode() ?: $order->getProductCode();

        $data = $order->getData();
        $data['ship_at']      = $shipment->getShipAt();
        $data['product_code'] = $productCode;

        $order->setData($data);

        $this->orderRepositoryInterface->save($order);
    }
}
