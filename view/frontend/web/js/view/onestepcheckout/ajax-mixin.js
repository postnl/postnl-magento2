/* jshint ignore:start */
define([
    'mage/utils/wrapper',
    'uiRegistry'
], function (
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

                if (!housenumberElement) {
                    return result;
                }

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
