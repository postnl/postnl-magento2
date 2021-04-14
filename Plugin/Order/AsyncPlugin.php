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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
namespace TIG\PostNL\Plugin\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Sales\Model\ResourceModel\Grid;
use Magento\Sales\Model\ResourceModel\Provider\NotSyncedDataProviderInterface;
use TIG\PostNL\Model\OrderRepository;

class AsyncPlugin extends Grid
{
    /**
     * @var array
     */
    private $notSyncedIds;

    /**
     * @var NotSyncedDataProviderInterface
     */
    private $notSyncedDataProvider;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param OrderRepository                     $orderRepository
     * @param ResourceConnection                  $resourceConnection
     * @param Context                             $context
     * @param string                              $mainTableName
     * @param string                              $gridTableName
     * @param string                              $orderIdField
     * @param array                               $joins
     * @param array                               $columns
     * @param null                                $connectionName
     * @param NotSyncedDataProviderInterface|null $notSyncedDataProvider
     */
    public function __construct(
        OrderRepository $orderRepository,
        ResourceConnection $resourceConnection,
        Context $context,
        $mainTableName,
        $gridTableName,
        $orderIdField,
        array $joins = [],
        array $columns = [],
        $connectionName = null,
        NotSyncedDataProviderInterface $notSyncedDataProvider = null
    ) {
        parent::__construct(
            $context,
            $mainTableName,
            $gridTableName,
            $orderIdField,
            $joins,
            $columns,
            $connectionName,
            $notSyncedDataProvider
        );

        $this->orderRepository = $orderRepository;
        $this->resourceConnection = $resourceConnection;
        $this->notSyncedDataProvider = $notSyncedDataProvider;
    }

    public function beforeRefreshBySchedule()
    {
        $this->notSyncedIds = $this->notSyncedDataProvider->getIds($this->mainTableName, $this->gridTableName);
    }

    public function afterRefreshBySchedule()
    {
        foreach ($this->notSyncedIds as $orderId) {
            $postNLOrder = $this->orderRepository->getByOrderId($orderId);
            if (!$postNLOrder) {
                continue;
            }
            $shipAt = $postNLOrder->getShipAt();
            $productCode = $postNLOrder->getProductCode();
            $confirmed = $postNLOrder->getConfirmed();
            $connection = $this->resourceConnection->getConnection();

            $binds = [
                'tig_postnl_ship_at'        => $shipAt,
                'tig_postnl_product_code'   => $productCode,
                'tig_postnl_confirmed'      => $confirmed,
            ];

            $where = [$connection->quoteIdentifier('entity_id') . '=?' => $orderId];
            $connection->update('sales_order_grid', $binds, $where);
        }
    }
}
