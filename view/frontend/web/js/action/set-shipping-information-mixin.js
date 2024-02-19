/* jshint ignore:start */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function (
    $,
    wrapper,
    quote
) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();

            if (shippingAddress === undefined || !shippingAddress) {
                return originalAction();
            }

            if (shippingAddress.customAttributes === undefined) {
                return originalAction();
            }

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }
            // < M2.3.0
            if (shippingAddress.customAttributes !== undefined && shippingAddress.customAttributes.tig_housenumber !== undefined) {
                shippingAddress['extension_attributes']['tig_housenumber']          = shippingAddress.customAttributes.tig_housenumber;
                shippingAddress['extension_attributes']['tig_housenumber_addition'] = shippingAddress.customAttributes.tig_housenumber_addition;
            }
            // >= M2.3.0
            if (shippingAddress.customAttributes[0] === undefined) {
                return originalAction();
            }

            if (shippingAddress.customAttributes[0].attribute_code === 'tig_housenumber') {
                shippingAddress['extension_attributes']['tig_housenumber']          = shippingAddress.customAttributes[0].value;
                shippingAddress['extension_attributes']['tig_housenumber_addition'] = shippingAddress.customAttributes[1].value;
            }

            return originalAction();
        });
    };
});
/* jshint ignore:end */