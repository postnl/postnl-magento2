define([
    'ko',
    'jquery',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote'
], function (
    ko,
    $,
    selectShippingMethodAction,
    checkoutData,
    quote
) {
    var deliveryOptionsAreLoading = ko.observable(false),
        pickupOptionsAreLoading = ko.observable(false),
        fee = ko.observable(null),
        currentSelectedShipmentType = ko.observable(null),
        config = window.checkoutConfig.shipping.postnl,
        pickupAddress = ko.observable(null);

    var isLoading = ko.computed(function () {
        return deliveryOptionsAreLoading() || pickupOptionsAreLoading();
    });

    quote.shippingMethod.subscribe(function (shippingMethod) {
        if (!shippingMethod) {
            return;
        }

        if (shippingMethod.carrier_code !== 'tig_postnl') {
            currentSelectedShipmentType(shippingMethod.carrier_code);
        }

        if (shippingMethod.carrier_code === 'tig_postnl') {
            return;
        }

        pickupAddress(null);
    });

    /**
     * When switching from delivery to pickup, the fee must be removed.
     */
    currentSelectedShipmentType.subscribe(function (value) {
        if (value == 'pickup') {
            fee(0);
        }
    });

    return {
        deliveryPrice: ko.observable(0),
        pickupPrice: ko.observable(0),
        statedDeliveryPrice: ko.observable(0),
        pickupDate: ko.observable(0),
        deliveryOptionsAreAvailable: ko.observable(true),
        deliveryOptionsAreLoading: deliveryOptionsAreLoading,
        pickupOptionsAreAvailable: ko.observable(true),
        pickupOptionsAreLoading: pickupOptionsAreLoading,
        currentSelectedShipmentType: currentSelectedShipmentType,
        currentOpenPane: ko.observable(config.default_tab),
        defaultTab: config.default_tab,
        pickupAddress: pickupAddress,
        isLoading: isLoading,
        method: ko.observable(null),
        fee: fee,
        deliveryFee: ko.observable(0),
        pickupFee: ko.observable(0),
        statedDeliveryFee: ko.observable(0)
    };
});
