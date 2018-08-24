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

        if ($postNLOrder->getShipAt()) {
            $this->orderGrid->refresh($order->getId());
        }

        return $this;
    }
}
