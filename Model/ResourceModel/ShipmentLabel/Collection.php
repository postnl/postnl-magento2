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
