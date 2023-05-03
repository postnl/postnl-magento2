<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Fields;

class HousenumberAddition implements FieldInterface
{
    /**
     * {@inheritDoc}
     */
    public function get($scope)
    {
        return [
            'component'  => 'Magento_Ui/js/form/element/abstract',
            'config'     => [
                'customScope' => $scope . '.custom_attributes',
                'template'    => 'ui/form/field',
                'elementTmpl' => 'TIG_PostNL/form/element/input-addition'
            ],
            'provider'   => 'checkoutProvider',
            'dataScope'  => $scope . '.custom_attributes.tig_housenumber_addition',
            // @codingStandardsIgnoreLine
            'label'      => __('Addition'),
            'sortOrder'  => '120',
            'validation' => [
                'required-entry' => false,
            ],
            'visible'    => true
        ];
    }
}
