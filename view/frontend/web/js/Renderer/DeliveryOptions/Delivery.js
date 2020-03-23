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
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'jquery',
    'TIG_PostNL/js/Helper/AddressFinder',
    'TIG_PostNL/js/Helper/Logger',
    'TIG_PostNL/js/Helper/State',
    'TIG_PostNL/js/Models/TimeFrame'
], function (
    Component,
    ko,
    quote,
    priceUtils,
    $,
    AddressFinder,
    Logger,
    State,
    TimeFrame
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/DeliveryOptions/Delivery',
            postcode: null,
            country: null,
            street: null,
            hasAddress:false,
            deliverydays: ko.observableArray([]),
            odd: false
        },

        initObservable: function () {
            this._super().observe([
                'deliverydays',
                'postcode',
                'country',
                'street',
                'hasAddress',
                'selectedOption'
            ]);

            AddressFinder.subscribe(function (address, oldAddress) {
                State.deliveryOptionsAreAvailable(false);
                if (!window.checkoutConfig.shipping.postnl.shippingoptions_active) {
                    return;
                }

                if (!address || JSON.stringify(address) == JSON.stringify(oldAddress)) {
                    return;
                }

                if (address.country !== 'NL' && address.country !== 'BE') {
                    return;
                }

                this.getDeliverydays(address);
            }.bind(this));

            /**
             * Deselect the selected delivery option when a different option type is being selected.
             */
            State.currentSelectedShipmentType.subscribe(function (shipmentType) {
                if (shipmentType !== 'delivery') {
                    this.selectedOption(null);
                }
            }.bind(this));

            /**
             * Save the selected delivery option
             *
             * @param TimeFrame
             */
            this.selectedOption.subscribe(function (value) {
                this.saveSelectedOption(value);
            }.bind(this));

            return this;
        },

        saveSelectedOption: function (value) {
            if (value === null || value === undefined) {
                return;
            }

            State.currentSelectedShipmentType('delivery');

            var fee = null;
            if (!value.fallback) {
                if (value.hasFee()) {
                    fee = value.getFee();
                }
            }

            State.fee(fee);
            State.deliveryFee(fee);

            $(document).trigger('compatible_postnl_deliveryoptions_save_before');
            $.ajax({
                method : 'POST',
                url    : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_save,
                data   : {
                    address: AddressFinder(),
                    type   : (typeof value.fallback !== 'undefined') ? 'fallback' : 'delivery',
                    date   : value.date,
                    option : value.option,
                    from   : value.from,
                    to     : value.to,
                    country: (typeof value.address !== 'undefined') ? value.address.country : AddressFinder().country,
                    stated_address_only: +$('#postnl_stated_address_only_checkbox').is(':checked')
                }
            }).done(function (response) {
                $(document).trigger('compatible_postnl_deliveryoptions_save_done', {response: response});
            });
        },

        /**
         * Retrieve the Deliverydays from PostNL.
         *
         * @param address
         */
        getDeliverydays: function (address) {
            State.deliveryOptionsAreLoading(true);
            var self = this;
            if (self.getDeliveryDayRequest !== undefined) {
                self.getDeliveryDayRequest.abort('avoidMulticall')
            }

            self.getDeliveryDayRequest = $.ajax({
                method: 'POST',
                url : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_timeframes,
                data : {address: address}
            }).done(function (data) {
                State.deliveryOptionsAreAvailable(true);
                State.deliveryPrice(data.price);

                var error = false;
                if (data.error) {
                    error = data.error;
                }

                if (error) {
                    Logger.error(error);
                    data = ko.utils.arrayMap(data.timeframes, function (fallback) {
                        return fallback;
                    });
                    this.deliverydays(data);
                    State.currentOpenPane('delivery');
                    return;
                }

                data = ko.utils.arrayMap(data.timeframes, function (day) {
                    return ko.utils.arrayMap(day, function (timeFrame) {
                        timeFrame.address = address;
                        return new TimeFrame(timeFrame);
                    });
                });

                this.deliverydays(data);
            }.bind(this)).fail(function (data) {
                if (data.statusText !== 'avoidMulticall') {
                    State.deliveryOptionsAreAvailable(false);
                    Logger.error(data);
                }
            }).always(function () {
                State.deliveryOptionsAreLoading(false);
            });
        },

        isRowSelected: function ($data) {
            return JSON.stringify(this.selectedOption()) == JSON.stringify($data);
        },

        isOdd: function () {
            this.odd = !this.odd;

            return this.odd;
        },

        statedAddressOnlyFee: function () {
            var fee = window.checkoutConfig.shipping.postnl.stated_address_only_fee;

            if (!fee) {
                return false;
            }

            return priceUtils.formatPrice(fee, quote.getPriceFormat());
        },

        handleStatedAddressOnlyFee: function () {
            this.saveSelectedOption(this.selectedOption());
            var statedAddressOnly = +$('#postnl_stated_address_only_checkbox').is(':checked');
            if (statedAddressOnly) {
                State.statedDeliveryFee(window.checkoutConfig.shipping.postnl.stated_address_only_fee);
            } else {
                State.statedDeliveryFee(0);
            }

            return true;
        },

        canUseStatedAddressOnly: ko.computed(function () {
            var isActive = window.checkoutConfig.shipping.postnl.stated_address_only_active;

            var address = AddressFinder();
            var isNL = (address !== null && address !== false && address.country === 'NL');

            return isActive === 1 && isNL;
        })
    });
});
