<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Validate;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

class ApiCredentials extends Field
{
    /** @var string */
    //@codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/validate/apiCredentials.phtml';

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();
        $element->unsCanUseWebsiteValue();
        $element->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    //@codingStandardsIgnoreLine
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('postnl/config_validate/apiCredentials');
    }

    /**
     * Generate collect button html
     *
     * @return string
     *
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        $layout = $this->getLayout();
        $buttonBlock = $layout->createBlock(Button::class);
        $buttonBlock->setData(
            [
                'id' => 'validate_api_credentials',
                //@codingStandardsIgnoreLine
                'label' => __('Validate API Credentials'),
            ]
        );

        return $buttonBlock->toHtml();
    }
}
