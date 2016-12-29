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
define(['uiComponent', 'ko', 'Magento_Checkout/js/model/quote', 'jquery'], function (Component, ko, quote, $) {
    'use strict';
    var self;
    var address = false;
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/delivery',
            postalCode : null,
            countryCode : null,
            street : null,
            hasAddress :false,
            deliverydays: []
        },

        initObservable : function () {
            self = this;

            this._super().observe([
                'deliverydays',
                'postalCode',
                'countryCode',
                'street',
                'hasAddress',
                'selectedRow'
            ]);

            this.hasAddress = ko.computed (function () {
                if (!quote.shippingAddress()) {
                    address = false;
                    return this;
                }

                self.postalCode  = quote.shippingAddress().postcode;
                self.countryCode = quote.shippingAddress().countryId;
                self.street      = quote.shippingAddress().street;

                if (!self.street) {
                    //  Create own data object.
                    self.street = {
                        street : {
                            0: $("input[name*='street[0]']").val(),
                            1: $("input[name*='street[1]']").val()
                        }
                    };
                }

                if (!self.postalCode || !self.countryCode || !self.street) {
                    address = false;
                    return this;
                }

                if (
                    self.postalCode.length > 0 ||
                    self.countryCode.length > 0 ||
                    (self.street.length > 0 && self.street.street[0] !== '')
                ) {
                    address = true;
                }

                if (address) {
                    self.getDeliverydays(
                        {
                            postcode: self.postalCode,
                            country : self.countryCode,
                            street  : self.street
                        }
                    );
                }
            });

            return this;
        },

        setDeliverydays : function (data) {
          this.deliverydays(data);
        },

        getDeliverydays : function (address) {
            jQuery.ajax({
                method: "POST",
                url : '/postnl/deliveryoptions',
                data : {type: 'deliverydays', address: address}
            }).done( function (data) {
                self.setDeliverydays(data);
            }).fail( function (data) {
                console.log(data);
            });
        }
    });
});