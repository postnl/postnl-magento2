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

            return  (address.country === 'NL' || address.country === 'BE');
        }),

        canUsePickupLocations: ko.computed(function () {
            var isActive = window.checkoutConfig.shipping.postnl.pakjegemak_active;
            var pickupOptionsAreAvailable = State.pickupOptionsAreAvailable();

            var address = AddressFinder();
            var isNL = (address !== null && address !== false && address.country === 'NL');

            return isActive === 1 && isNL && pickupOptionsAreAvailable;
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
