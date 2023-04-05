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

    return function (setBillingAddressAction) {
        return wrapper.wrap(setBillingAddressAction, function (originalAction) {
            var billingAddress = quote.billingAddress();

            if (billingAddress === undefined || !billingAddress) {
                return originalAction();
            }

            if (billingAddress.customAttributes === undefined) {
                return originalAction();
            }

            if (billingAddress['extension_attributes'] === undefined) {
                billingAddress['extension_attributes'] = {};
            }
            // < M2.3.0
            if (billingAddress.customAttributes !== undefined && billingAddress.customAttributes.tig_housenumber !== undefined) {
                billingAddress['extension_attributes']['tig_housenumber']          = billingAddress.customAttributes.tig_housenumber;
                billingAddress['extension_attributes']['tig_housenumber_addition'] = billingAddress.customAttributes.tig_housenumber_addition;
            }
            // >= M2.3.0
            if (billingAddress.customAttributes[0] === undefined) {
                return originalAction();
            }

            if (billingAddress.customAttributes[0].attribute_code === 'tig_housenumber') {
                billingAddress['extension_attributes']['tig_housenumber']          = billingAddress.customAttributes[0].value;
                billingAddress['extension_attributes']['tig_housenumber_addition'] = billingAddress.customAttributes[1].value;
            }

            return originalAction();
        });
    };
});
/* jshint ignore:end */