<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Comment;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class LabelSize extends Template implements RendererInterface
{
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/comment/labelSize.phtml';

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

    /**
     * @return string
     */
    public function getA4ExampleUrl()
    {
        return $this->_assetRepo->getUrl('TIG_PostNL::pdf/A4Label.pdf');
    }

    /**
     * @return string
     */
    public function getA6ExampleUrl()
    {
        return $this->_assetRepo->getUrl('TIG_PostNL::pdf/A6Label.pdf');
    }
}
