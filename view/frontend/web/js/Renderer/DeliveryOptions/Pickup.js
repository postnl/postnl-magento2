/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'TIG_PostNL/js/Helper/AddressFinder',
    'TIG_PostNL/js/Helper/Logger',
    'TIG_PostNL/js/Helper/State'
], function (
    Component,
    ko,
    quote,
    $,
    AddressFinder,
    Logger,
    State
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/DeliveryOptions/Pickup',
            postalCode : null,
            countryCode : null,
            street : null,
            hasAddress :false,
            pickupAddresses: []
        },

        daysSorting: {
            'monday': 1,
            'tuesday': 2,
            'wednesday': 3,
            'thursday': 4,
            'friday': 5,
            'saturday': 6,
            'sunday': 7
        },

        daysLabels: {
            monday: $.mage.__('Monday'),
            tuesday: $.mage.__('Tuesday'),
            wednesday: $.mage.__('Wednesday'),
            thursday: $.mage.__('Thursday'),
            friday: $.mage.__('Friday'),
            saturday: $.mage.__('Saturday'),
            sunday: $.mage.__('Sunday')
        },

        initObservable : function () {
            this._super().observe([
                'pickupAddresses',
                'postalCode',
                'countryCode',
                'street',
                'hasAddress',
                'selectedOption'
            ]);

            /**
             * Subscribe to address changes.
             */
            AddressFinder.subscribe(function (address) {
                if (!address) {
                    return;
                }

                this.getPickupAddresses({
                    postcode: address.postalCode,
                    country : address.countryCode,
                    street  : address.street
                });
            }.bind(this));

            /**
             * Save the selected pickup option
             */
            this.selectedOption.subscribe(function (value) {
                State.selectShippingMethod();

                var dataObject = value.data;
                var selectedFrom = '15:00:00';

                if (value.type == 'PGE') {
                    selectedFrom = '9:00:00';
                }

                $.ajax({
                    method: 'POST',
                    url: window.checkoutConfig.shipping.postnl.urls.deliveryoptions_save,
                    data: {
                        type: 'pickup',
                        name : dataObject.Name,
                        RetailNetworkID: dataObject.RetailNetworkID,
                        LocationCode : dataObject.LocationCode,
                        from: selectedFrom,
                        address: dataObject.Address,
                        customerData : AddressFinder()
                    }
                });
            });

            return this;
        },

        setPickupAddresses : function (data) {
            this.pickupAddresses(data);
        },

        /**
         * Retrieve the pickup addresses from the backend.
         *
         * @param address
         */
        getPickupAddresses : function (address) {
            State.pickupOptionsAreLoading(true);

            jQuery.ajax({
                method: 'POST',
                url : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_locations,
                data : {address: address}
            }).done(function (data) {
                State.pickupOptionsAreAvailable(true);

                data = ko.utils.arrayMap(data, function (data) {
                    return new Location(data);
                });

                this.setPickupAddresses(data);
            }.bind(this)).fail(function (data) {
                State.pickupOptionsAreAvailable(false);
                Logger.error(data);
            }).always(function () {
                State.pickupOptionsAreLoading(false);
            });
        },

        isRowSelected: function ($data) {
            return JSON.stringify(this.selectedOption()) == JSON.stringify($data);
        },

        getDistanceText: function (distance) {
            var text = '';

            distance = parseInt(distance);

            if (distance < 1000 && distance > 0) {
                text = distance + ' m';
            }

            if (distance > 1000) {
                text = parseFloat(Math.round(distance / 100) / 10).toFixed(1) + ' km';
            }

            return text;
        },

        /**
         * Convert the OpeningHours object to a format readable by Knockout
         *
         * @param OpeningHours
         * @returns {*}
         */
        getOpeningHours: function (OpeningHours) {
            var output = [];

            $.each(OpeningHours, function (index, record) {
                var data = {
                    index : index,
                    day   : this.daysLabels[index.toLowerCase()],
                    hours : this.getHours(record)
                };

                output.push(data);
            }.bind(this));

            return this.sortDays(output);
        },

        /**
         * Format the hours to a knockout readable format.
         *
         * @param hours
         * @returns {Array}
         */
        getHours: function (hours) {
            var output = [];
            $.each(hours, function (index, hour) {
                output.push(hour[0]);
            });

            return output;
        },

        /**
         * The data does not comes sorted by day from PostNL, so sort it.
         *
         * @param data
         * @returns {*}
         */
        sortDays: function (data) {
            return data.sort(function (a, b) {
                var day1 = a.index.toLowerCase();
                var day2 = b.index.toLowerCase();
                return this.daysSorting[day1] > this.daysSorting[day2];
            }.bind(this));
        },

        /**
         * Toggle the pickup hours visibility.
         *
         * @param $data
         */
        toggle: function ($data) {
            $data.expanded(!$data.expanded());
        },

        /**
         *
         * @param $deliveryOptions
         * @returns {boolean}
         */
        hasPGE: function ($deliveryOptions) {
            var pgeActive = window.checkoutConfig.shipping.postnl.pakjegemak_express_active;
            var pgeInDeliveryOptions = ($deliveryOptions['string'].indexOf('PGE') >= '0');

            return pgeActive && pgeInDeliveryOptions;
        },
    });

    function Location(data)
    {
        $.each(data, function (key, value) {
            this[key] = value;
        }.bind(this));

        this.expanded = ko.observable(false);

        this.toggle = function () {
            this.expanded(!this.expanded());
        };
    }
});

