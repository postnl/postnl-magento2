<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

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
        $confirmedAt = $item['tig_postnl_confirmed'];

        if ($confirmedAt === null) {
            return false;
        }

        return (bool) $confirmedAt;
    }
}
