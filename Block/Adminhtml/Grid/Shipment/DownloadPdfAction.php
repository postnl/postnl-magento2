<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Shipment;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\BlockInterface;

class DownloadPdfAction extends Template implements BlockInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::shipment/grid/DownloadPdfAction.phtml';

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->getUrl('postnl/shipment/massPrintShippingLabel');
    }

    /**
     * @return string
     */
    public function getPrintPackingSlipUrl()
    {
        return $this->getUrl('postnl/shipment/massPrintPackingslip');
    }
}
