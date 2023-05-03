<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Fields;

class Housenumber implements FieldInterface
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
                'elementTmpl' => 'TIG_PostNL/form/element/input'
            ],
            'provider'   => 'checkoutProvider',
            'dataScope'  => $scope . '.custom_attributes.tig_housenumber',
            // @codingStandardsIgnoreLine
            'label'      => __('House number'),
            'sortOrder'  => '115',
            'validation' => [
                'required-entry' => true,
            ],
            'visible'    => true
        ];
    }
}
