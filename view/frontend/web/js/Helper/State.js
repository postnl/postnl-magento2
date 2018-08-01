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

        if (shippingMethod.carrier_code === 'tig_postnl') {
            return;
        }

        $("input[name*='deliveryday']").prop('checked', false);
        $("input[name*='pickuplocation']").prop('checked', false);

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
        deliveryOptionsAreAvailable: ko.observable(true),
        deliveryOptionsAreLoading: deliveryOptionsAreLoading,
        pickupOptionsAreAvailable: ko.observable(true),
        pickupOptionsAreLoading: pickupOptionsAreLoading,
        currentSelectedShipmentType: currentSelectedShipmentType,
        currentOpenPane: ko.observable(config.is_deliverydays_active ? 'delivery' : 'pickup'),
        pickupAddress: pickupAddress,
        isLoading: isLoading,
        method: ko.observable(null),
        fee: fee,
        deliveryFee: ko.observable(0),
        pickupFee: ko.observable(0),

        /**
         * Make sure that the PostNL shipping method gets selected when the customer picks a delivery or pickup option.
         *
         * @returns {boolean}
         */
        selectShippingMethod: function () {
            selectShippingMethodAction(this.method());
            checkoutData.setSelectedShippingRate(this.method().carrier_code + '_' + this.method().method_code);

            return true;
        }
    };
});
