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
