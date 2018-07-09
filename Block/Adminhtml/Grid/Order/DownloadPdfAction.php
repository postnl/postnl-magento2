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

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\BlockInterface;

class DownloadPdfAction extends Template implements BlockInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::order/grid/DownloadPdfAction.phtml';

    /**
     * @return string
     */
    public function getConfirmAndPrintLabelsUrl()
    {
        return $this->getUrl('postnl/order/CreateShipmentsConfirmAndPrintShippingLabels');
    }

    /**
     * @return string
     */
    public function getConfirmAndPrintPackingSlipUrl()
    {
        return $this->getUrl('postnl/order/CreateShipmentsConfirmAndPrintPackingSlip');
    }

    /**
     * @return string
     */
    public function getPrintPackingSlipUrl()
    {
        return $this->getUrl('postnl/order/CreateShipmentsAndPrintPackingSlip');
    }
}
