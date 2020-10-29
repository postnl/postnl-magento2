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
 * to support@postcodeservice.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
/* jshint ignore:start */
define([
    'jquery',
    'mage/utils/wrapper',
    'uiRegistry'
], function (
    $,
    wrapper,
    Registry
) {
    'use strict';

    return function (Component) {
        return Component.extend({
            collectParams: function() {
                var result = this._super();

                var housenumberElement = Registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber');
                var housenumberAdditionElement = Registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber_addition');

                if (result['shippingAddress'] && result['shippingAddress']['street'] && result['shippingAddress']['street'].length > 0 && housenumberElement.value()) {
                    result['shippingAddress']['street'][1] = housenumberElement.value();

                    if (housenumberAdditionElement.value()) {
                        result['shippingAddress']['street'][2] = housenumberAdditionElement.value();
                    }
                }

                return result;
            }
        });
    };
});
/* jshint ignore:end */
