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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define([
    'jquery',
    'Magento_Ui/js/form/components/group',
    'ko',
    'uiRegistry'
], function (
    $,
    uiComponent,
    ko,
    Registry
) {
    'use strict';
    return uiComponent.extend({
        defaults : {
            template  : 'TIG_Postcode/checkout/field-group',
            isLoading : false,
            message   : ko.observable(null),
            imports   : {
                observePostcode    : '${ $.parentName }.postcode-field-group.field-group.postcode:value',
                observeHousenumber : '${ $.parentName }.postcode-field-group.field-group.housenumber:value',
                observeCountry     : '${ $.parentName }.country_id:value'
            },
            sameCall  : false,
            timer     : undefined
        },

        initialize : function () {
            this._super()
                ._setClasses();

            return this;
        },

        showAddressFields : function (showFields) {
            var shippingFields = [
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0',
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city'
            ];
            var billingFields = [
                'checkout.steps.billing-step.payment.payments-list.checkmo-form.form-fields.street.0',
                'checkout.steps.billing-step.payment.payments-list.checkmo-form.form-fields.city'
            ];

            Registry.get(shippingFields, function (streetElement, cityElement) {
                if (showFields === true) {
                    streetElement.enable();
                    cityElement.enable();
                } else {
                    streetElement.disable();
                    cityElement.disable();
                }
            });

            Registry.get(billingFields, function (streetElement, cityElement) {
                if (showFields === true) {
                    streetElement.enable();
                    cityElement.enable();
                } else {
                    streetElement.disable();
                    cityElement.disable();
                }
            });
        },

        updateFieldData : function () {
            var self = this;

            // Only apply the postcode check for NL
            var country = $("select[name*='country_id']").val();
            if (this.customScope === 'billingAddresscheckmo') {
                country = $("select[name*='country_id']").eq(1).val();
            }

            if (country !== 'NL') {
                self.showAddressFields(true);
                $('.postnl_hidden').show();
                return;
            } else {
                $('.postnl_hidden').hide();
            }

            // Set a timer because we don't want to make a call at every change
            if (typeof this.timer !== 'undefined') {
                clearTimeout(this.timer);
            }

            this.timer = setTimeout(function () {
                self.setFieldData();
            }, 1000);
        },

        setFieldData : function () {
            var self = this;
            var formData = self.getFormData();

            if (formData !== false) {
                self.isLoading(true);
                self.showAddressFields(false);
                self.getAddressData(formData);
            }
        },

        getFormData : function () {
            var postcode;
            var housenumber;

            var postcodeRegex = /^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i;
            // Wait for the form to load, once loaded get the values of housenumber and postcode
            Registry.get([
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber',
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.postcode'
                ], function (housenumberElement, postcodeElement) {
                housenumber = housenumberElement.value();
                postcode = postcodeElement.value();
            });

            if ($.isNumeric(housenumber) && postcodeRegex.test(postcode)) {
                return [housenumber, postcode];
            }

            return false;
        },

        getAddressData : function (formData) {
            var self = this;

            if (self.request !== undefined) {
                self.request.abort();
            }

            // Make the request to get the streetname and city
            self.request = $.ajax({
                method:'GET',
                url: window.checkoutConfig.shipping.postnl.urls.address_postcode,
                data: {
                    housenumber: formData[0],
                    postcode: formData[1]
                }
            }).done(function (data) {
                self.handleResponse(data);
            }).fail(function (data) {
                console.error("Error receiving response from SAM");
                self.showAddressFields(true);
            }).always(function (data) {
                self.isLoading(false);
            });
        },

        handleResponse : function (data) {
            var self = this;
            if (data.status === false) {
                // to do when a wrong address is supplied, give an error message
                console.error(data.error);
                self.showAddressFields(true);
            }
            // If the data is correct, set the streetname and city
            if (data.streetName && data.city) {
                Registry.get(self.parentName + '.street.0').set('value', data.streetName);
                Registry.get(self.parentName + '.city').set('value', data.city);

                // Trigger change for subscripe methods.
                $("input[name*='street[0]']").trigger('change');
                $("input[name*='city']").trigger('change');
            }
        },

        initObservable : function () {
            this._super().observe(['isLoading']);

            return this;
        },

        observeHousenumber : function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observePostcode : function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeCountry : function (value) {
            this.updateFieldData();
        }
    });
});
