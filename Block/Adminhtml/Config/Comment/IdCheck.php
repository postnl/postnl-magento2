<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Comment;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class IdCheck extends Template implements RendererInterface
{
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/comment/idCheck.phtml';

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
