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
    'TIG_PostNL/js/Models/TimeFrame',
    'Magento_Checkout/js/action/set-shipping-information'
], function (
    Component,
    ko,
    quote,
    priceUtils,
    $,
    AddressFinder,
    Logger,
    State,
    TimeFrame,
    setShippingInformationAction
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
            odd: false,
            currentDeliveryAddress: ko.observable(null)
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

            sessionStorage.setItem("postnlDeliveryOption", JSON.stringify(value));
            sessionStorage.removeItem("postnlPickupOption");

            State.currentSelectedShipmentType('delivery');

            var fee = null;
            if (!value.fallback && !value.letterbox_package && !value.eps && !value.gp) {
                if (value.hasFee !== undefined && value.hasFee()) {
                    fee = value.getFee();
                }
            }

            State.fee(fee);
            State.deliveryFee(fee);

            var type = 'delivery';
            if (typeof value.fallback !== 'undefined') {
                type = 'fallback';
            }
            if (typeof value.letterbox_package !== 'undefined') {
                type = 'Letterbox Package';
            }

            if (typeof value.eps !== 'undefined') {
                type = 'EPS';
            }

            if (typeof value.gp !== 'undefined') {
                type = 'GP';
            }

            $(document).trigger('compatible_postnl_deliveryoptions_save_before');
            $.ajax({
                method : 'POST',
                url    : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_save,
                data   : {
                    address: AddressFinder(),
                    type   : type,
                    date   : value.date,
                    option : value.option,
                    from   : value.from,
                    to     : value.to,
                    country: (typeof value.address !== 'undefined') ? value.address.country : AddressFinder().country,
                    stated_address_only: +$('#postnl_stated_address_only_checkbox').is(':checked')
                }
            }).done(function (response) {
                $(document).trigger('compatible_postnl_deliveryoptions_save_done', {response: response});
                if (window.checkoutConfig.shipping.postnl.onestepcheckout_active) {
                    setShippingInformationAction();
                }
            });
        },

        /**
         * Retrieve the Deliverydays from PostNL.
         *
         * @param address
         */
        getDeliverydays: function (address) {
            // Unfortunately the "this" context shows the "Window" context, while the "this" context
            // should be the UiClass context. When assigning the "this" context to something, both
            // the assigned AND the this context will change to the UiClass.
            var self = this;

            // Avoid getting delivery days multiple times.
            // Regex for street used, housenumbers can be stored in the street field and the actual street is not relevant.
            var addressAsString = JSON.stringify({
                'postcode': address.postcode,
                'street': address.street[0].replace(/\D/g,''),
                'housenumber': address.housenumber
            });
            if (this.deliverydays() !== undefined && this.currentDeliveryAddress() === addressAsString) {
                return;
            }

            this.currentDeliveryAddress(addressAsString);

            State.deliveryOptionsAreLoading(true);

            if (self.getDeliveryDayRequest !== undefined) {
                self.getDeliveryDayRequest.abort('avoidMulticall');
            }

            // About to receive new delivery days. Deselect the current one.
            this.selectedOption(null);

            self.getDeliveryDayRequest = $.ajax({
                method: 'POST',
                url : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_timeframes,
                data : {address: address}
            }).done(function (data) {
                // If the deliverydays are reloaded, the first one is automatically selected.
                // Show this by switching to the delivery pane.
                State.currentOpenPane('delivery');

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

                    this.selectFirstDeliveryOption();
                    return;
                }
                if (data === '') {
                    return false;
                }

                if (data.letterbox_package === true) {
                    data  = ko.utils.arrayMap(data.timeframes, function (letterbox_package) {
                        return letterbox_package;
                    });
                    this.deliverydays(data);
                    this.selectFirstDeliveryOption();
                    return;
                }

                // Return delivery moment of type EPS if the country is an EPS country
                if (typeof data.timeframes[0][0] !== 'undefined' && "eps" in data.timeframes[0][0]) {
                    data  = ko.utils.arrayMap(data.timeframes, function (eps) {
                        return eps;
                    });
                    this.deliverydays(data);
                    State.currentOpenPane('delivery');
                    this.selectFirstDeliveryOption();
                    return;
                }

                // Return delivery moment of type GP if the country is an Global Pack country
                if (typeof data.timeframes[0][0] !== 'undefined' && "gp" in data.timeframes[0][0]) {
                    data  = ko.utils.arrayMap(data.timeframes, function (gp) {
                        return gp;
                    });
                    this.deliverydays(data);
                    State.currentOpenPane('delivery');
                    this.selectFirstDeliveryOption();
                    return;
                }

                data = ko.utils.arrayMap(data.timeframes, function (day) {
                    return ko.utils.arrayMap(day, function (timeFrame) {
                        timeFrame.address = address;
                        return new TimeFrame(timeFrame);
                    });
                });

                this.deliverydays(data);
                this.selectFirstDeliveryOption();
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
            var isNL = (address !== null && address !== false && address.country === 'NL' || address.country === 'BE');

            return isActive === 1 && isNL;
        }),

        selectFirstDeliveryOption: function () {
            // Only select the first option if there is none defined
            if (this.selectedOption() !== undefined && this.selectedOption() != null || sessionStorage.postnlPickupOption) {
                return;
            }
            var deliveryDays = this.deliverydays();


            if(sessionStorage.postnlDeliveryOption) {
                var previousOption = JSON.parse(sessionStorage.postnlDeliveryOption);
                this.selectedOption(previousOption)
                var indexes = this.getPreviousDay(previousOption, deliveryDays);
            }

            if (indexes) {
                this.selectedOption(deliveryDays[indexes[0]][indexes[1]]);
            }

            if (deliveryDays !== undefined && deliveryDays[0] !== undefined && deliveryDays[0][0] !== undefined && sessionStorage.postnlDeliveryOption === undefined) {
                this.selectedOption(deliveryDays[0][0]);
            }
        },

        getPreviousDay: function (option, deliveryDays) {
            var result;
            $.each(deliveryDays, function (deliveryDaysindex, value) {
                if (Array.isArray(value)) {
                    $.each(value, function (index, value) {
                        if (value.day === option.day && value.from === option.from && value.to === option.to) {
                            result = [deliveryDaysindex, index];
                            return false;
                        }
                    })
                }
            })

            return result;
        }
    });
});
