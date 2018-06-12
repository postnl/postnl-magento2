<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
