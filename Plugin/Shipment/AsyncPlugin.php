<?php

namespace TIG\PostNL\Plugin\Shipment;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Sales\Model\ResourceModel\Grid;
use Magento\Sales\Model\ResourceModel\Provider\NotSyncedDataProviderInterface;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

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
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var PostNLShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @param PostNLShipmentRepository            $shipmentRepository
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
        PostNLShipmentRepository $shipmentRepository,
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

        $this->resourceConnection = $resourceConnection;
        $this->notSyncedDataProvider = $notSyncedDataProvider;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function beforeRefreshBySchedule()
    {
        $this->notSyncedIds = $this->notSyncedDataProvider->getIds($this->mainTableName, $this->gridTableName);
    }

    public function afterRefreshBySchedule()
    {
        foreach ($this->notSyncedIds as $shipmentId) {
            $postNLShipment = $this->shipmentRepository->getByShipmentId($shipmentId);
            if (!$postNLShipment) {
                continue;
            }

            $connection = $this->resourceConnection->getConnection();

            $binds = [
                'tig_postnl_ship_at'        => $postNLShipment->getShipAt(),
                'tig_postnl_product_code'   => $postNLShipment->getProductCode(),
                'tig_postnl_confirmed'      => $postNLShipment->getConfirmed(),
                'tig_postnl_confirmed_at'   => $postNLShipment->getConfirmedAt(),
                'tig_postnl_barcode'        => $postNLShipment->getMainBarcode(),
            ];

            $where = [$connection->quoteIdentifier('entity_id') . '=?' => $shipmentId];
            $tableName = $connection->getTableName('sales_shipment_grid');
            $connection->update($tableName, $binds, $where);
        }
    }
}
