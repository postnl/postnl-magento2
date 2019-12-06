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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
