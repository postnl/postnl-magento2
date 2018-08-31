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
/* jshint ignore:start */
define([
    'jquery',
    'Magento_Ui/js/form/components/group',
    'ko',
    'uiRegistry'
], function(
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

        disableAddressFields : function () {
            // TODO: Make this compatible with less than 3 address fields
            var streetfields = [
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0',
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city'
            ];
            Registry.get(streetfields, function(element1, element2) {
                element1.disable();
                element2.disable();
            });
        },

        enableAddressFields : function () {
            // TODO: Make this compatible with less than 3 address fields
            var streetfields = [
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0',
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city'
            ];
            Registry.get(streetfields, function(element1, element2) {
                element1.enable();
                element2.enable();
            });
        },

        updateFieldData : function () {
            var self = this;

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

            var results = false;
            var formData = self.getFormData();

            if (formData !== false) {
                results = self.getAddressData(formData);
            }

            if (results !== false) {
                self.disableAddressFields();
            } else {
                self.enableAddressFields();
            }
        },

        getFormData : function () {
            var postcode;
            var housenumber;

            var postcodeRegex = /^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i;

            Registry.get([
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber',
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.postcode'
                ], function(housenumberElement, postcodeElement) {
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

            self.request = $.ajax({
                method:'GET',
                url: window.checkoutConfig.shipping.postnl.urls.address_postcode,
                data: {
                    housenumber: formData[0],
                    postcode: formData[1]
                }
            }).done(function (data) {
                console.log(data);
            });
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
            if (value) {
                this.updateFieldData();
            }
        }
    });
});
/* jshint ignore:end */