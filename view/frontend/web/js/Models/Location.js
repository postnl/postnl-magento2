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
    'TIG_PostNL/js/Helper/State'
], function (
    ko,
    $,
    State
) {
    var daysLabels = {
        monday: $.mage.__('Monday'),
        tuesday: $.mage.__('Tuesday'),
        wednesday: $.mage.__('Wednesday'),
        thursday: $.mage.__('Thursday'),
        friday: $.mage.__('Friday'),
        saturday: $.mage.__('Saturday'),
        sunday: $.mage.__('Sunday')
    };

    var daysSorting = {
        'monday': 1,
        'tuesday': 2,
        'wednesday': 3,
        'thursday': 4,
        'friday': 5,
        'saturday': 6,
        'sunday': 7
    };

    return function (data) {
        $.each(data, function (key, value) {
            this[key] = value;
        }.bind(this));

        this.expanded = ko.observable(false);

        /**
         * Toggle the pickup hours visibility.
         */
        this.toggle = function () {
            if (State.currentOpenPane() !== 'pickup') {
                return;
            }

            this.expanded(!this.expanded());
        };

        /**
         * Format the distance to the location
         *
         * @returns {string}
         */
        this.getDistanceText = function () {
            var text = '',
                distance = parseInt(this.Distance);

            if (distance < 1000 && distance > 0) {
                text = distance + ' m';
            }

            if (distance > 1000) {
                text = parseFloat(Math.round(distance / 100) / 10).toFixed(1) + ' km';
            }

            return text;
        };

        /**
         * Retrieve the address including the house number extension if applicable.
         *
         * @returns {*[]}
         */
        this.getStreet = function () {
            var houseNumber = this.Address.HouseNr;

            if (this.Address.HouseNrExt) {
                houseNumber += ' ' + this.Address.HouseNrExt;
            }

            return [this.Address.Street, houseNumber];
        };

        /**
         * Convert the OpeningHours object to a format readable by Knockout
         *
         * @returns {*}
         */
        this.getOpeningHours = function () {
            var output = [];

            $.each(this.OpeningHours, function (index, record) {
                var data = {
                    index : index,
                    day   : daysLabels[index.toLowerCase()],
                    hours : this.getHours(record)
                };

                output.push(data);
            }.bind(this));

            return this.sortDays(output);
        };

        /**
         * Format the hours to a knockout readable format.
         *
         * @param hours
         * @returns {Array}
         */
        this.getHours = function (hours) {
            var output = [];

            $.each(hours, function (index, hour) {
                output.push(hour[0]);
            });

            return output;
        };

        /**
         * The data does not comes sorted by day from PostNL, so sort it.
         *
         * @param data
         * @returns {*}
         */
        this.sortDays = function (data) {
            return data.sort(function (a, b) {
                var day1 = a.index.toLowerCase();
                var day2 = b.index.toLowerCase();
                return daysSorting[day1] > daysSorting[day2];
            }.bind(this));
        };
    };
});
