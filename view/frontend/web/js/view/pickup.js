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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
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
            template: 'TIG_PostNL/pickup',
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
                $.ajax({
                    method: 'POST',
                    url: '/postnl/deliveryoptions/save',
                    data: {
                        type: 'pickup',
                        name : value.Name,
                        RetailNetworkID: value.RetailNetworkID,
                        LocationCode : value.LocationCode,
                        address: value.Address
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
                url : '/postnl/deliveryoptions/pickup',
                data : {address: address}
            }).done(function (data) {
                State.pickupOptionsAreLoading(false);

                data = ko.utils.arrayMap(data, function (data) {
                    return new Location(data);
                });

                this.setPickupAddresses(data);
            }.bind(this)).fail(function (data) {
                Logger.error(data);
            });
        },

        isRowSelected: function ($data) {
            return JSON.stringify(this.selectedOption()) == JSON.stringify($data);
        },

        /**
         * Convert the OpeningHours object to a format readable by Knockout
         *
         * @param OpeningHours
         * @returns {*}
         */
        getOpeningHours: function (OpeningHours) {
            var output = [], record, hours;

            $.each(OpeningHours, function (index, record) {
                output.push({
                    day   : index,
                    hours : this.getHours(record)
                });
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
                var day1 = a.day.toLowerCase();
                var day2 = b.day.toLowerCase();
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
        }
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

