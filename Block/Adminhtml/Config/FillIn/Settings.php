<?php
declare(strict_types=1);

namespace TIG\PostNL\Block\Adminhtml\Config\FillIn;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Store\Model\StoreManagerInterface;
use function __;

class Settings extends Fieldset
{
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        ?SecureHtmlRenderer $secureRenderer,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $authSession, $jsHelper, $data, $secureRenderer);
    }

    protected function _getHeaderHtml($element)
    {
        $html = parent::_getHeaderHtml($element);
        $html .= '<p>'
            . __(
                'With this functionality your customers can easily and automatically fill in their shipping address via their PostNL account.'
            )
            . '</p><p>'
            . __('This functionality is only available for consumers with a Dutch shipping address.')
            . '</p><p>'
            . __('Click the following link to activate the functionality: ')
            . $this->getLink()
            . '</p>';

        return $html;
    }

    private function getLink()
    {
        $storeUrl = $this->storeManager->getStore()->getBaseUrl();

        return '<a href="https://dil-business-portal.postnl.nl/checkout-prefill?referrer=wcplugin&url=' . $storeUrl
            . '" target="_blank">https://dil-business-portal.postnl.nl/checkout-prefill?referrer=wcplugin&url='
            . $storeUrl .
            '</a>';
    }
}
