<?php

namespace TIG\PostNL\Model\ResourceModel\ShipmentLabel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use TIG\PostNL\Model\AbstractModel;
use TIG\PostNL\Model\ShipmentLabel;

class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ShipmentLabel', 'TIG\PostNL\Model\ResourceModel\ShipmentLabel');
    }

    /**
     * Cleanup labels older then $date (4 months).
     *
     * @param $date
     * @return void
     */
    public function cleanupExpiredLabels($date): void
    {
        $parentIds = $this->getConnection()->fetchCol(
            $this->getConnection()
                ->select()
                ->from($this->getConnection()->getTableName('tig_postnl_shipment'), 'entity_id')
                ->where(AbstractModel::FIELD_UPDATED_AT . ' < ?', $date)
        );

        if ($parentIds !== []) {
            $this->getConnection()->update(
                $this->getMainTable(),
                [ShipmentLabel::FIELD_LABEL => null],
                [ShipmentLabel::FIELD_PARENT_ID . " IN (?)" => array_keys($parentIds)]
            );
        }
    }
}
