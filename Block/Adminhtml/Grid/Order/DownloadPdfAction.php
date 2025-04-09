<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\BlockInterface;

/**
 * @api
 */
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
