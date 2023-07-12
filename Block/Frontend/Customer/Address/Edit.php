<?php

namespace TIG\PostNL\Block\Frontend\Customer\Address;

use Magento\Backend\Block\Template;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\BlockInterface;

class Edit extends Template implements BlockInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::customer/address/Postcode.phtml';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Template\Context $context
     * @param UrlInterface     $urlBuilder
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function getPostcodeUrl()
    {
        return $this->urlBuilder->getUrl('postnl/address/postcode', ['_secure' => true]);
    }
}
