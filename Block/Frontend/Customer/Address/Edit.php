<?php

namespace TIG\PostNL\Block\Frontend\Customer\Address;

use Magento\Customer\Block\Address\Edit as EditBlock;
use Magento\Framework\View\Element\BlockInterface;

/**
 * @api
 */
class Edit extends EditBlock implements BlockInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::customer/address/Postcode.phtml';

    /**
     * @return string
     */
    public function getPostcodeUrl()
    {
        return $this->_urlBuilder->getUrl('postnl/address/postcode', ['_secure' => true]);
    }
}
