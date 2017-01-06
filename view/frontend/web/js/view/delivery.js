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
    'TIG_PostNL/js/Helper/Logger'
], function (
    Component,
    ko,
    quote,
    $,
    AddressFinder,
    Logger
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/delivery',
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

            AddressFinder.subscribe( function (address) {
                if (!address) {
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
            this.selectedOption.subscribe( function (value) {
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
            $.ajax({
                method: 'POST',
                url : '/postnl/deliveryoptions',
                data : {
                    type: 'deliverydays',
                    address: address
                }
            }).done(function (data) {
                Logger.info(data);
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
