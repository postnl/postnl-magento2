<?php

namespace TIG\PostNL\Observer\SalesOrderSaveAfter;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\GridInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Api\Data\OrderInterface;

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
     * @var ScopeConfigInterface
     */
    private $globalConfig;

    /**
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param GridInterface            $orderGrid
     * @param ScopeConfigInterface     $globalConfig
     */
    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        GridInterface $orderGrid,
        ScopeConfigInterface $globalConfig
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderGrid = $orderGrid;
        $this->globalConfig = $globalConfig;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');

        /** @var OrderInterface $postNLOrder */
        $postNLOrder = $this->orderRepositoryInterface->getByOrderId($order->getId());
        if (!$postNLOrder) {
            return $this;
        }

        if ($postNLOrder->getShipAt() && !$this->globalConfig->getValue('dev/grid/async_indexing')) {
            $this->orderGrid->refresh($order->getId());
        }

        return $this;
    }
}
