<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Mageplaza;

use TIG\PostNL\Plugin\Postcodecheck\LayoutProcessor as AddressProcessor;

class LayoutProcessor
{
    /**
     * @var AddressProcessor
     */
    private $processor;

    /**
     * LayoutProcessor constructor.
     *
     * @param AddressProcessor $processor
     */
    public function __construct(AddressProcessor $processor)
    {

        $this->processor = $processor;
    }

    /**
     * @param $subject
     * @param $jsLayout
     *
     * @return array
     */
    //@codingStandardsIgnoreLine
    public function afterProcess($subject, $jsLayout)
    {
        $jsLayout = $this->processMageplazaBillingFields($jsLayout);

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return array
     */
    private function processMageplazaBillingFields($jsLayout)
    {
        $billingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                          ['children']['billingAddress']['children']['billing-address-fieldset']['children'];

        $fieldSet = $this->processor->processAddress($billingFields, 'billingAddress', []);
        $billingFields['postcode-field-group'] = $fieldSet['postcode-field-group'];

        $this->processor->setAdditionalClass($billingFields, 'postcode', true);
        $this->processor->setAdditionalClass($billingFields, 'city');
        $this->processor->setAdditionalClass($billingFields, 'street');

        return $jsLayout;
    }
}
