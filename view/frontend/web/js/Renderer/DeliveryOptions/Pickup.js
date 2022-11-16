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
    'jquery',
    'TIG_PostNL/js/Helper/AddressFinder',
    'TIG_PostNL/js/Helper/Logger',
    'TIG_PostNL/js/Helper/State',
    'TIG_PostNL/js/Models/Location',
    'Magento_Checkout/js/action/set-shipping-information'
], function (
    Component,
    ko,
    quote,
    $,
    AddressFinder,
    Logger,
    State,
    Location,
    setShippingInformationAction
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/DeliveryOptions/Pickup',
            postcode : null,
            country : null,
            street : null,
            hasAddress :false,
            pickupAddresses: ko.observableArray([]),
            currentLocationAddress: null
        },

        initObservable : function () {
            this._super().observe([
                'pickupAddresses',
                'postcode',
                'country',
                'street',
                'hasAddress',
                'selectedOption'
            ]);

            /**
             * Subscribe to address changes.
             */
            AddressFinder.subscribe(function (address) {
                State.deliveryOptionsAreAvailable(false);
                if (!window.checkoutConfig.shipping.postnl.shippingoptions_active ||
                    (
                        window.checkoutConfig.shipping.postnl.pakjegemak_active == "0" &&
                        window.checkoutConfig.shipping.postnl.pakjegemak_be_active == "0"
                    ) ||
                    !address
                ) {
                    return;
                }

                if (address.country !== 'NL' && address.country !== 'BE') {
                    return;
                }

                this.getPickupAddresses(address);
            }.bind(this));

            /**
             * Deselect the selected pickup option when a different option type is being selected.
             */
            State.currentSelectedShipmentType.subscribe(function (shipmentType) {
                if (shipmentType !== 'pickup') {
                    this.selectedOption(null);
                }
            }.bind(this));

            /**
             * Save the selected pickup option
             */
            this.selectedOption.subscribe(function (value) {
                if (value === null || value === undefined) {
                    return;
                }

                if (typeof value.data !== 'undefined') {
                    sessionStorage.setItem("postnlPickupOption", JSON.stringify(value.data.Name));
                    sessionStorage.removeItem("postnlDeliveryOption");
                }

                State.currentSelectedShipmentType('pickup');

                if (typeof value.data === 'undefined') {
                    var dataObject = value,
                    selectedFrom = '15:00:00',
                        option = 'PG';
                }

                if (typeof value.data !== 'undefined') {
                    var dataObject = value.data,
                        selectedFrom = '15:00:00',
                        option = 'PG';
                }

                var fee = 0;
                if (dataObject !== 'undefined' && dataObject.hasFee()) {
                    fee = dataObject.getFee();
                }


                State.fee(fee);
                State.pickupFee(fee);

                State.pickupAddress({
                    company: dataObject.Name,
                    prefix: '',
                    firstname: '',
                    lastname: '',
                    suffix: '',
                    street: dataObject.getStreet(),
                    city: dataObject.Address.City,
                    region: '',
                    postcode: dataObject.Address.Zipcode,
                    countryId: dataObject.Address.Countrycode,
                    telephone: ''
                });

                // this trigger makes the extension compatible with onestep checkout.
                $(document).trigger('compatible_postnl_deliveryoptions_save_before');

                $.ajax({
                    method: 'POST',
                    url: window.checkoutConfig.shipping.postnl.urls.deliveryoptions_save,
                    data: {
                        type: 'pickup',
                        option: option,
                        name : dataObject.Name,
                        country : dataObject.Address.Countrycode,
                        RetailNetworkID: dataObject.RetailNetworkID,
                        LocationCode : dataObject.LocationCode,
                        from: selectedFrom,
                        address: dataObject.Address,
                        customerData : AddressFinder(),
                        stated_address_only: 0
                    }
                }).done(function (response) {
                    $(document).trigger('compatible_postnl_deliveryoptions_save_done', {response: response});
                    if (window.checkoutConfig.shipping.postnl.onestepcheckout_active) {
                        setShippingInformationAction();
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
            var self = this;
            // Avoid getting delivery days multiple times
            var addressAsString = JSON.stringify({'postcode': address.postcode, 'housenumber': address.housenumber});
            if (this.pickupAddresses() !== undefined && this.currentLocationAddress === addressAsString) {
                return;
            }

            this.currentLocationAddress      = addressAsString;
            State.pickupOptionsAreLoading(true);

            if (self.getLocationsRequest !== undefined) {
                self.getLocationsRequest.abort('avoidMulticall');
            }

            self.getLocationsRequest = $.ajax({
                method: 'POST',
                url : window.checkoutConfig.shipping.postnl.urls.deliveryoptions_locations,
                data : {address: address}
            }).done(function (data) {
                if (data.error) {
                    Logger.error(data.error);
                    State.pickupOptionsAreAvailable(false);
                    State.currentOpenPane('delivery');
                    this.selectFirstPickupOption();
                    return false;
                }

                if (data.locations.error) {
                    Logger.error(data.locations.error);
                    State.pickupOptionsAreAvailable(false);
                    State.currentOpenPane('delivery');
                    this.selectFirstPickupOption();
                    return false;
                }

                State.pickupOptionsAreAvailable(true);
                State.pickupPrice(data.price);

                var isDeliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
                if (isDeliveryDaysActive) {
                    State.pickupDate(data.pickup_date);
                }

                data = data.locations.slice(0, 5);
                data = ko.utils.arrayMap(data, function (data) {
                    return new Location(data);
                });

                this.setPickupAddresses(data);
                this.selectFirstPickupOption();
            }.bind(this)).fail(function (data) {
                if (data.statusText !== 'avoidMulticall') {
                    State.pickupOptionsAreAvailable(false);
                    Logger.error(data);
                }
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

            if (this.selectedOption() === null || this.selectedOption() === undefined || this.selectedOption().data === undefined) {
                return false;
            }
            return JSON.stringify(this.selectedOption().data) == JSON.stringify($data);
        },

        selectFirstPickupOption: function () {
            // Only select the first option if there is none defined
            if (this.selectedOption() !== undefined && this.selectedOption() != null) {
                return;
            }

            var optionIndex     = '';
            var pickupAddresses = this.pickupAddresses();

            if (sessionStorage.postnlPickupOption) {
                State.currentOpenPane('pickup');
                var previousOption = JSON.parse(sessionStorage.postnlPickupOption)

               $.each(pickupAddresses, function (index,value) {
                  if (value.Name === previousOption) {
                      optionIndex = index;
                  }
               });
            }

            if (optionIndex !== '') {
                var result = {data: pickupAddresses[optionIndex], type: "PG"};
                this.selectedOption(result);
                $('.tig-postnl-pickup-radio:eq(' + optionIndex + ')').prop('checked',true);
            }
        },
    });
});

