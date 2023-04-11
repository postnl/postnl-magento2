<?php
declare(strict_types=1);

namespace TIG\PostNL\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * frontend_model for Delivery Date Off
 * class DeliveryDateOff
 */
class DeliveryDateOff extends AbstractFieldArray
{
    /**
     * @return void
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'delivery_date_off',
            ['label' => __('Date'), 'class' => 'delivery-date-off-picker validate-select admin__control-text input-text input-date']
        );

        $this->_addAfter = false;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $html = parent::_getElementHtml($element);
        $html .= $this->addDatePickerToHtml();

        return $html;
    }

    /**
     * @return string
     */
    private function addDatePickerToHtml(): string
    {
        return '<script type="text/javascript">
                require(["jquery", "jquery/ui", "mage/calendar"], function ($) {
                    $(function() {
                        function bindDatePicker() {
                            $(".delivery-date-off-picker").datepicker( { dateFormat: "dd-mm-yy" } );
                        }
                        bindDatePicker();
                        $("button.action-add").on("click", function(e) {
                            bindDatePicker();
                        });
                    });
                });
            </script>';
    }
}
