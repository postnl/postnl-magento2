<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Validate;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

class InternationalAddressValidation extends Field
{
    /** @var string */
    //@codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/validate/internationalAddressValidation.phtml';

    /**
     * @param Context                 $context
     * @param array                   $data
     */
    public function __construct(
        Context             $context,
        array               $data = []
    ) {
        parent::__construct($context, $data);
    }

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
        return $this->getUrl('postnl/config_validate/internationalAddressValidation');
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
        $layout      = $this->getLayout();
        $buttonBlock = $layout->createBlock(Button::class);
        $buttonBlock->setData(
            [
                'id' => 'validate_international_address_check',
                //@codingStandardsIgnoreLine
                'label' => __('Validate International Address Check'),
            ]
        );

        return $buttonBlock->toHtml();
    }
}
