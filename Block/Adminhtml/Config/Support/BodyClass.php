<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Support;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\BlockInterface;

/**
 * @api
 */
class BodyClass extends Template implements BlockInterface
{
    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        if ($this->_request->getParam('section') == 'tig_postnl') {
            $this->pageConfig->addBodyClass('postnl-config-page');
        }

        return parent::_prepareLayout();
    }
}
