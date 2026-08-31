<?php

namespace TIG\PostNL\Block\Adminhtml\Config\General;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ApiKey extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setReadonly(true);
        return $element->getElementHtml();
    }
}
