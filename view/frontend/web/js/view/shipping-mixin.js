/*global alert*/
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'TIG_PostNL/js/Renderer/DeliveryOptions/Delivery',
    'TIG_PostNL/js/Renderer/DeliveryOptions/Pickup',
    'mage/translate'
], function (
    $,
    quote,
    delivery,
    pickup,
    $t
) {
    'use strict';

    return function (Component) {
        return Component.extend({
            validateShippingInformation: function () {
                var originalResult = this._super();
                var postcodeRegex = /^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i;

                if (quote.shippingMethod().carrier_code !== 'tig_postnl') {
                    return originalResult;
                }

                if (delivery().deliverydays().length === 0 && pickup().pickupAddresses().length === 0) {
                    return originalResult;
                }

                // Check if a valid zipcode has been entered
                if (quote.shippingAddress().countryId === 'NL' && !postcodeRegex.test(quote.shippingAddress().postcode)) {
                    this.errorValidationMessage(
                        $t('Please enter a valid zipcode.')
                    );

                    return false;
                }

                // deliverydays and pickupAddresses are not emptied when another country is selected.
                if (quote.shippingAddress().countryId !== 'NL' && quote.shippingAddress().countryId !== 'BE') {
                    return originalResult;
                }

                // Returns undefined if no option is checked.
                var checkedOption = $('.tig-postnl-delivery-radio:checked, .tig-postnl-pickup-radio:checked').val();

                if (checkedOption === undefined) {
                    this.errorValidationMessage(
                        $t('Please select a PostNL delivery option before continuing. If no options are visible, please make sure you\'ve entered your address information correctly.')
                    );

                    return false;
                }

                return originalResult;
            }
        });
    };
});
