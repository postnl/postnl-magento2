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
namespace TIG\PostNL\Plugin\Postcodecheck;

use TIG\PostNL\Plugin\Postcodecheck\Fields\Factory;
use TIG\PostNL\Config\Provider\Webshop;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class LayoutProcessor
{
    /**
     * @var Factory
     */
    private $fieldFactory;

    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Factory $factory,
        Webshop $webshop,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->fieldFactory  = $factory;
        $this->webshopConfig = $webshop;
        $this->scopeConfig   = $scopeConfig;
    }

    /**
     * @param       $subject
     * @param array $jsLayout
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function afterProcess($subject, array $jsLayout)
    {
        if (!$this->webshopConfig->getIsAddressCheckEnabled()) {
            return $jsLayout;
        }

        $jsLayout = $this->processShippingFields($jsLayout);
        $jsLayout = $this->processBillingFields($jsLayout);

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processShippingFields($jsLayout)
    {
        $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                           ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        $shippingFields = $this->processAddress($shippingFields, 'shippingAddress', []);

        $this->setAdditionalClass($shippingFields, 'postcode', true);
        $this->setAdditionalClass($shippingFields, 'city');
        $this->setAdditionalClass($shippingFields, 'street');

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processBillingFields($jsLayout)
    {
        $billingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                          ['children']['payment']['children']['payments-list']['children'];

        if (!isset($billingFields) || !$this->isDisplayBillingOnPaymentMethodAvailable()) {
            return $this->processSingleBillingForm($jsLayout) ?: $jsLayout;
        }

        // @codingStandardsIgnoreStart
        foreach ($billingFields as $key => &$billingForm) {
            if (strpos($key, '-form') === false) {
                continue;
            }

            $billingForm['children']['form-fields']['children'] = $this->processAddress(
                $billingForm['children']['form-fields']['children'],
                $billingForm['dataScopePrefix'],
                []
            );

            $this->setAdditionalClass($billingForm['children']['form-fields']['children'], 'postcode', true);
            $this->setAdditionalClass($billingForm['children']['form-fields']['children'], 'city');
            $this->setAdditionalClass($billingForm['children']['form-fields']['children'], 'street');
        }
        // @codingStandardsIgnoreEnd

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processSingleBillingForm($jsLayout)
    {
        $billingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                          ['children']['payment']['children']['afterMethods']['children']['billing-address-form'];

        if (!isset($billingFields)) {
            return false;
        }

        $billingFields['children']['form-fields']['children'] = $this->processAddress(
            $billingFields['children']['form-fields']['children'],
            $billingFields['dataScopePrefix'],
            []
        );

        $this->setAdditionalClass($billingFields['children']['form-fields']['children'], 'postcode', true);
        $this->setAdditionalClass($billingFields['children']['form-fields']['children'], 'city');
        $this->setAdditionalClass($billingFields['children']['form-fields']['children'], 'street');

        return $jsLayout;
    }

    /**
     * @param $fieldset
     * @param $scope
     * @param $deps
     *
     * @return mixed
     */
    private function processAddress($fieldset, $scope, $deps)
    {
        $fieldset['postcode-field-group'] = [
            'component' => 'TIG_PostNL/js/view/form/fields',
            'type'      => 'group',
            'provider'  => 'checkoutProvider',
            'sortOrder' => '65',
            'config'    => [
                'customScope' => $scope,
                'template'    => 'TIG_PostNL/checkout/field-group',
                'additionalClasses' => $this->webshopConfig->getCheckoutCompatibleForAddressCheck()
            ],
            'deps'      => $deps,
            'children'  => [
                'field-group' => [
                    'component'   => 'uiComponent',
                    'displayArea' => 'field-group',
                    'children'  => [
                        'postcode'             => $fieldset['postcode'],
                        'housenumber'          => $this->fieldFactory->get('housenumber', $scope),
                        'housenumber_addition' => $this->fieldFactory->get('addition', $scope)
                    ]
                ]
            ],
            'dataScope' => '',
            'visible'   => true
        ];

        return $fieldset;
    }

    /**
     * @param      $fields
     * @param      $section
     * @param bool $disableRequired
     */
    private function setAdditionalClass(&$fields, $section, $disableRequired = false)
    {
        $additionalClass = null;
        if (isset($fields[$section]['config']['additionalClasses'])) {
            $additionalClass = $fields[$section]['config']['additionalClasses'];
        }

        if ($section == 'street' || $section == 'city') {
            $additionalClass = $additionalClass . ' ' . 'postnl_postcodecheck_disableable';
        }

        if ($disableRequired) {
            $fields[$section]['validation']['required-entry'] = false;
        }

        $fields[$section]['config']['additionalClasses'] = $additionalClass;
    }

    /**
     * @return bool
     */
    private function isDisplayBillingOnPaymentMethodAvailable()
    {
        return (bool) !$this->scopeConfig->getValue(
            'checkout/options/display_billing_address_on',
            ScopeInterface::SCOPE_STORE
        );
    }
}
