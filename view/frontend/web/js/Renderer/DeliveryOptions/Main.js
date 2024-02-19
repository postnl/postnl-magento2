define([
    'uiComponent',
    'ko',
    'TIG_PostNL/js/Helper/State',
    'TIG_PostNL/js/Helper/AddressFinder',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function (
    Component,
    ko,
    State,
    AddressFinder,
    quote,
    priceUtils
) {
    var formatPrice = function (price) {
        return priceUtils.formatPrice(price, quote.getPriceFormat());
    };

    return Component.extend({

        defaults: {
            template: 'TIG_PostNL/DeliveryOptions/Main',
            shipmentType: State.currentOpenPane,
            deliveryPrice: State.deliveryPrice,
            pickupPrice: State.pickupPrice,
            statedDeliveryPrice: State.statedDeliveryPrice,
            pickupDate: State.pickupDate,
            deliveryFee: State.deliveryFee,
            pickupFee: State.pickupFee,
            statedDeliveryFee: State.statedDeliveryFee
        },


        initObservable: function () {
            this.selectedMethod = ko.computed(function () {
                var method = quote.shippingMethod();
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                return selectedMethod;
            }, this);

            this._super().observe([
                'shipmentType'
            ]);

            this.isLoading = State.isLoading;

            return this;
        },

        canUsePostNLDeliveryOptions: ko.computed(function () {
            var deliveryOptionsActive = window.checkoutConfig.shipping.postnl.shippingoptions_active;
            var deliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
            var pakjegemakActive = window.checkoutConfig.shipping.postnl.pakjegemak_active === 1;

            return deliveryOptionsActive && (deliveryDaysActive || pakjegemakActive);
        }),

        canUseDeliveryOptions: ko.computed(function () {
            var address = AddressFinder();

            if (address === null || address === false) {
                return false;
            }

            return true;
        }),

        canUsePickupLocations: ko.computed(function () {
            var isActive = window.checkoutConfig.shipping.postnl.pakjegemak_active;
            var isActiveBe = window.checkoutConfig.shipping.postnl.pakjegemak_be_active;
            var pickupOptionsAreAvailable = State.pickupOptionsAreAvailable();

            var address = AddressFinder();
            var isNL = (address !== null && address !== false && address.country === 'NL');
            var isBE = (address !== null && address !== false && address.country === 'BE');

            return ((isActive === 1 && isNL) || (isActiveBe === 1 && isBE)) && pickupOptionsAreAvailable;
        }),

        setDelivery: function () {
            State.currentOpenPane('delivery');
        },

        setPickup: function () {
            State.currentOpenPane('pickup');
        },

        formatPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    });
});
