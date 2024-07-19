<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Form\Field;


use TIG\PostNL\Service\Validation\AlternativeDelivery;

class AlternativeDeliveryForCountryMap extends AlternativeDeliveryMap
{
    protected function _prepareToRender(): void
    {
        $this->addColumn(AlternativeDelivery::DELIVERY_MAP_COUNTRY,
            ['label' => __('Country'), 'class' => 'required-entry']
        );
        parent::_prepareToRender();
    }
}
