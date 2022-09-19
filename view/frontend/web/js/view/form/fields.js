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
            timer : undefined,
            value : ko.observable('')
        },

        initialize : function () {
            this._super()
            ._setClasses();

            if (window.checkoutConfig.shipping.postnl.checkout_extension == 'mageplaza') {
                this.setMageplazaPrefilter();
            }

            return this;
        },

        setMageplazaPrefilter : function () {
            $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                var optionsArray;
                if (options.url.indexOf('checkout-information') >= 0) {
                    optionsArray = JSON.parse(options.data);
                    if (Object.keys(optionsArray.customerAttributes).length < 1) {
                        optionsArray.customerAttributes = {
                            tig_housenumber: $(".tig-postnl-field-group div[name='shippingAddress.custom_attributes.tig_housenumber'] input").val(),
                            tig_housenumber_addition: $(".tig-postnl-field-group div[name='shippingAddress.custom_attributes.tig_housenumber_addition'] input").val()
                        };
                    }
                    options.data = JSON.stringify(optionsArray);
                }

                if (options.url.indexOf('payment-information') >= 0 && options.data) {
                    optionsArray = JSON.parse(options.data);
                    if (optionsArray.billingAddress !== undefined && optionsArray.billingAddress.extension_attributes === undefined) {
                        optionsArray.billingAddress.extension_attributes = {
                            tig_housenumber: $(".tig-postnl-field-group div[name='billingAddress.custom_attributes.tig_housenumber'] input").val(),
                            tig_housenumber_addition: $(".tig-postnl-field-group div[name='billingAddress.custom_attributes.tig_housenumber_addition'] input").val()
                        };
                    }
                    options.data = JSON.stringify(optionsArray);
                }
            }.bind(this));
        },

        enableAddressFields : function (enableFields) {
            var fields = [
                this.parentName + '.street.0',
                this.parentName + '.city'
            ];

            Registry.get(fields, function (streetElement, cityElement) {
                if (enableFields === true) {
                    streetElement.enable();
                    cityElement.enable();
                } else {
                    streetElement.disable();
                    cityElement.disable();
                }
            });
        },

        hideAddressFields : function (hideFields) {
            var self = this;
            var fields = [
                this.parentName + '.street.1',
                this.parentName + '.street.2',
                this.parentName + '.street.3'
            ];

            // Hide or show every address line that's not the first address line, depending on the country settings.
            for (var i=0; i < fields.length; i++) {
                self.hideAddressField(hideFields, fields[i]);
            }
        },

        hideAddressField : function (hideFields, field) {
            Registry.get(field, function (addressLine) {
                if (hideFields === true) {
                    addressLine.hide();
                } else {
                    addressLine.show();
                }
            });
        },

        updateFieldData : function () {
            var self = this;

            var country;

            // Only apply the postcode check for NL
            Registry.get([this.parentName + '.country_id'], function (countryElement) {
                country = countryElement.value();
            });

            if (country !== 'NL' && country !== 'BE') {
                // In some countries housenumber is not required
                Registry.get([this.parentName + '.postcode-field-group.field-group.housenumber'], function (housenumberElement) {
                    housenumberElement.required(false);
                    housenumberElement.validation['required-entry'] = false;
                });
            } else {
                Registry.get([this.parentName + '.postcode-field-group.field-group.housenumber'], function (housenumberElement) {
                    housenumberElement.required(true);
                    housenumberElement.validation['required-entry'] = true;
                });
            }

            if (country !== 'NL') {
                self.enableAddressFields(true);
                self.hideAddressFields(false);

                return;
            } else {
                self.hideAddressFields(true);
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
                self.enableAddressFields(false);
                self.getAddressData(formData);
            } else {
                self.isLoading(false);
                self.enableAddressFields(true);
            }
        },

        getFormData : function () {
            var self = this;
            var postcode;
            var housenumber;

            var postcodeRegex = /^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i;

            // Wait for the form to load, once loaded get the values of housenumber and postcode
            var fields = [
                this.parentName + '.postcode-field-group.field-group.housenumber',
                this.parentName + '.postcode-field-group.field-group.postcode'
            ];

            Registry.get(fields, function (housenumberElement, postcodeElement) {
                housenumber = housenumberElement.value();
                postcode = postcodeElement.value();
            });

            if (!postcode || !housenumber) {
                return false;
            }

            if ($.isNumeric(housenumber) && postcodeRegex.test(postcode)) {
                return [housenumber, postcode];
            }

            if (self.request !== undefined || !postcodeRegex.test(postcode)) {
                self.handleError($.mage.__('Please enter a valid zipcode and housenumber.'));
            }

            return false;
        },

        getAddressData : function (formData) {
            var self = this;

            if (self.request !== undefined) {
                self.request.abort('avoidMulticall');
            }

            // Make the request to get the streetname and city
            self.request = $.ajax({
                method:'GET',
                url: window.checkoutConfig.shipping.postnl.urls.address_postcode,
                data: {
                    housenumber: formData[0],
                    postcode: formData[1]
                },
            }).done(function (data) {
                self.handleResponse(data);
            }).fail(function (data) {
                if (data.statusText !== 'avoidMulticall') {
                    var errorMessage = $.mage.__('Unexpected error occurred. Please fill in the address details manually.');
                    self.handleError(errorMessage);
                    self.enableAddressFields(true);
                }
            }).always(function (data) {
                self.isLoading(false);
            });
        },

        handleResponse : function (data) {
            var self = this;
            var errorMessage = $.mage.__('Unexpected error occurred. Please fill in the address details manually.');

            if (data.status === false) {
                errorMessage = $.mage.__('Sorry, we could not find your address with the zipcode and housenumber combination. If you are sure that the zipcode and housenumber are correct, please fill in the address details manually.');
            }

            if (data.streetName && data.city) {
                // If the data is correct, set the streetname and city
                Registry.get(self.parentName + '.street.0').set('value', data.streetName);
                Registry.get(self.parentName + '.city').set('value', data.city);

                // Trigger change for subscripe methods.
                $("input[name*='street[0]']").trigger('change');
                $("input[name*='city']").trigger('change');

                errorMessage = null;
            }

            if (data.error) {
                console.error(data.error);
            }

            self.handleError(errorMessage);
        },

        handleError : function (errorMessage) {
            var self = this;
            var error = $('.tig-postnl-validation-message');
            error.hide();

            if (errorMessage) {
                self.enableAddressFields(true);

                error.html(errorMessage).show();
            }
        },

        initObservable : function () {
            this._super().observe(['isLoading', 'value']);

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
            var self = this;

            var fields = [
                self.parentName + '.postcode-field-group.field-group.postcode',
                self.parentName + '.postcode-field-group.field-group.housenumber',
                self.parentName + '.postcode-field-group.field-group.housenumber_addition',
            ];

            Registry.get(fields, function (postcodeElement, housenumberElement, additionElement) {
                // Empty elements when the country has been switched
                if (value !== 'NL') {
                    housenumberElement.value('');
                    additionElement.value('');
                }

                // Next line is for initial load, before field is found in jQuery
                postcodeElement.additionalClasses['tig-postnl-full-width'] = (value !== 'NL');

                housenumberElement.visible(value === 'NL');
                additionElement.visible(value === 'NL');

                var postcodeField = $('.tig-postnl-field-group div[name$=postcode]');
                /* jshint ignore:start */
                value === 'NL' ? postcodeField.removeClass('tig-postnl-full-width') : postcodeField.addClass('tig-postnl-full-width');
                /* jshint ignore:end */
            });
            this.updateFieldData();
        }
    });
});
