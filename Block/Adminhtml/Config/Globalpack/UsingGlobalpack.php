<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Globalpack;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class UsingGlobalpack extends Template implements RendererInterface
{
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/globalpack/usingGlobalpack.phtml';

    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setElement($element);

        return $this->toHtml();
    }
}
