<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Form\Field;

use \Magento\Config\Block\System\Config\Form\Field as MagentoField;
use \Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends MagentoField
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function _getElementHtml(AbstractElement $element)
    {
        $html  = $element->getElementHtml();
        $value = $element->getData('value');
        $html .= $this->addColorPickerToHtml($element, $value);

        return $html;
    }

    /**
     * @param AbstractElement $element
     * @param                 $value
     *
     * @return string
     */
    private function addColorPickerToHtml(AbstractElement $element, $value)
    {
        // @codingStandardsIgnoreStart
        return '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                    var $el = $("#' . $element->getHtmlId() . '");
                    $el.css("backgroundColor", "'. $value .'");
                    $el.ColorPicker({
                        color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
            });
            </script>';
        // @codingStandardsIgnoreEnd
    }
}
