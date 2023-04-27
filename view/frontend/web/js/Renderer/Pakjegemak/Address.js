define([
    'Magento_Checkout/js/view/shipping-information/address-renderer/default',
    'ko',
    'jquery',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'TIG_PostNL/js/Helper/State'
], function (
    Component,
    ko,
    $,
    stepNavigator,
    sidebarModel,
    State
) {
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/Pakjegemak/Address',
            isVisible: false,
            address: {
                company: '',
                prefix: '',
                firstname: '',
                lastname: '',
                suffix: '',
                street: '',
                city: '',
                region: '',
                postcode: '',
                countryId: '',
                telephone: ''
            }
        },

        initObservable : function () {
            this._super().observe([
                'address',
                'isVisible'
            ]);

            this.address = State.pickupAddress;
            this.isVisible = ko.computed(function () {
                return State.currentSelectedShipmentType() === 'pickup';
            });

            return this;
        },

        back: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping');
        }
    });
});
