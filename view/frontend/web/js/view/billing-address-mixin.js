define([
    'jquery',
    'Magento_Checkout/js/model/quote'
], function (
    $,
    quote
) {
    'use strict';

    return function (Component) {
        return Component.extend({
            tigHouseNumber: function() {
                var customAttributes = quote.billingAddress().customAttributes;

                if (customAttributes && customAttributes.length > 0) {
                    var houseNumber = "";
                    var houseNumberAddition = "";

                    // check if custom attribute exists and is not empty
                    customAttributes.forEach(function (attribute) {
                        if (attribute.attribute_code === "tig_housenumber" && attribute.value !== "") {
                            houseNumber = attribute.value;
                        }

                        if (attribute.attribute_code === "tig_housenumber_addition" && attribute.value !== "") {
                            houseNumberAddition = attribute.value;
                        }
                    });

                    if (quote.billingAddress().countryId !== 'NL'){
                        return houseNumber + (houseNumberAddition ? " "  + houseNumberAddition : "");
                    }

                    if (quote.billingAddress().street[1] !== houseNumber && quote.shippingMethod() !== null && quote.shippingMethod().carrier_code === 'tig_postnl') {
                        quote.billingAddress().street.splice(1,1);
                    }

                    if (quote.billingAddress().street[2] !== houseNumberAddition && quote.shippingMethod() !== null && quote.shippingMethod().carrier_code === 'tig_postnl') {
                        quote.billingAddress().street.splice(1,1);
                    }

                    return houseNumber + (houseNumberAddition ? " "  + houseNumberAddition : "");
                }
            }
        });
    };
});
