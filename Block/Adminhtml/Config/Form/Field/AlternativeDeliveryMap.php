<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use TIG\PostNL\Service\Validation\AlternativeDelivery;

class AlternativeDeliveryMap extends AbstractFieldArray
{
    protected $optionField;

    protected ?string $uniqId = null;
    protected int $rowId = 0;

    protected function _prepareToRender()
    {
        // Uniq id for current config
        $this->uniqId = 'f' . uniqid();
        $this->rowId = 0;
        $this->addColumn(AlternativeDelivery::DELIVERY_MAP_SCALE,
            ['label' => __('Price Scale'), 'class' => 'required-entry validate-number validate-greater-than-zero']
        );
        $this->addColumn(AlternativeDelivery::DELIVERY_MAP_CODE, [
            'label' => __('Delivery Option'),
            'renderer' => $this->getOptionField(),
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Scale');
    }

    protected function getOptionField()
    {
        if (!$this->optionField) {
            $this->optionField = $this->getLayout()->createBlock(
                Option\DomesticDelivery::class,
                '',
                ['data' => ['is_render_to_js_template' => true, 'method' => $this->_data['method'] ?? null]]
            );
        }

        return $this->optionField;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $code = $row->getData(AlternativeDelivery::DELIVERY_MAP_CODE);
        $options = [];
        if ($code) {
            $options['option_' . $this->getOptionField()->calcOptionHash($code)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
        // Restore uniq id for options that was rewritten on save action
        $row->setData('_id', $this->uniqId . '_' . $this->rowId);
        $this->rowId++;
    }
}
