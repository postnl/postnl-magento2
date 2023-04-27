<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Shipment;

use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;

class ConfirmStatus extends AbstractGrid
{
    /**
     * @param $item
     *
     * @return string
     */
    //@codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        $confirmedAt = $this->getIsConfirmed($item);

        if (!$confirmedAt) {
            return __('Not confirmed');
        }

        return __('Confirmed');
    }

    /**
     * @param $item
     *
     * @return bool
     */
    private function getIsConfirmed($item)
    {
        $confirmedAt = $item['tig_postnl_confirmed_at'];

        if ($confirmedAt === null) {
            return false;
        }

        return true;
    }
}
