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
    'TIG_PostNL/js/Models/Location'
], function (
    Component,
    ko,
    quote,
    $,
    AddressFinder,
    Logger,
    State,
    Location
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
             * Deselect the selected pickup option when a different option type is being selected.
             */
            State.currentSelectedShipmentType.subscribe(function (shipmentType) {
                if (shipmentType != 'pickup') {
                    this.selectedOption(null);
                }
            }.bind(this));

            /**
             * Save the selected pickup option
             */
            this.selectedOption.subscribe(function (value) {
                if (value === null) {
                    return;
                }

                State.selectShippingMethod();
                State.currentSelectedShipmentType('pickup');

                State.pickupAddress({
                    company: value.Name,
                    prefix: '',
                    firstname: '',
                    lastname: '',
                    suffix: '',
                    street: value.getStreet(),
                    city: value.Address.City,
                    region: '',
                    postcode: value.Address.Zipcode,
                    countryId: value.Address.Countrycode,
                    telephone: ''
                });

                $.ajax({
                    method : 'POST',
                    url    : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_save,
                    data   : {
                        type            : 'pickup',
                        name            : value.Name,
                        RetailNetworkID : value.RetailNetworkID,
                        LocationCode    : value.LocationCode,
                        address         : value.Address,
                        customerData    : AddressFinder()
                    }
                });
            }.bind(this));

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

        /**
         * Check if the current row is selected.
         *
         * @param $data
         * @returns {boolean}
         */
        isRowSelected: function ($data) {
            return JSON.stringify(this.selectedOption()) == JSON.stringify($data);
        }
    });
});

