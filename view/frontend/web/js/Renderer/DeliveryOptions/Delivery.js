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
    'TIG_PostNL/js/Helper/State',
    'TIG_PostNL/js/Models/TimeFrame'
], function (
    Component,
    ko,
    quote,
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
            postalCode: null,
            countryCode: null,
            street: null,
            hasAddress:false,
            deliverydays: [],
            odd: false
        },

        initObservable: function () {
            this._super().observe([
                'deliverydays',
                'postalCode',
                'countryCode',
                'street',
                'hasAddress',
                'selectedOption'
            ]);

            AddressFinder.subscribe(function (address, oldAddress) {
                if (!address || JSON.stringify(address) == JSON.stringify(oldAddress)) {
                    return;
                }

                this.getDeliverydays({
                    postcode: address.postalCode,
                    country : address.countryCode,
                    street  : address.street
                });
            }.bind(this));

            /**
             * Save the selected delivery option
             */
            this.selectedOption.subscribe(function (value) {
                State.selectShippingMethod();

                $.ajax({
                    method: 'POST',
                    url: '/postnl/deliveryoptions/save',
                    data: {
                        type: 'delivery',
                        date : value.date,
                        option: value.option,
                        from: value.from,
                        to: value.to
                    }
                });
            });

            return this;
        },

        setDeliverydays: function (data) {
            this.deliverydays(data);
        },

        getDeliverydays: function (address) {
            State.deliveryOptionsAreLoading(true);

            $.ajax({
                method: 'POST',
                url : '/postnl/deliveryoptions/days',
                data : {address: address}
            }).done(function (data) {
                State.deliveryOptionsAreLoading(false);
                Logger.info(data);

                data = ko.utils.arrayMap(data, function (day) {
                    return ko.utils.arrayMap(day, function (timeFrame) {
                        return new TimeFrame(timeFrame);
                    });
                });

                this.setDeliverydays(data);
            }.bind(this)).fail(function (data) {
                Logger.error(data);
            });
        },

        isRowSelected: function ($data) {
            return JSON.stringify(this.selectedOption()) == JSON.stringify($data);
        },

        isOdd: function () {
            this.odd = !this.odd;

            return this.odd;
        }
    });
});
