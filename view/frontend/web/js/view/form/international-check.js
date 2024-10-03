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
        defaults: {
            template:       'TIG_Postcode/checkout/international-check',
            isLoading:      ko.observable(false),
            message:        ko.observable(null),
            messageClasses: ko.observable({}),
            addresses:      ko.observable([]),
            timeoutDelay:   800,
            timeout:        ko.observable(null),
            imports: {
                observePostcode:        '${ $.parentName }.postcode-field-group.field-group.postcode:value',
                observeHousenumber:     '${ $.parentName }.postcode-field-group.field-group.housenumber:value',
                observeCountry:         '${ $.parentName }.country_id:value',
                observeMagentoPostcode: '${ $.parentName }.postcode:value',
                observeMagentoCity:     '${ $.parentName }.city:value',
                observeMagentoStreet0:  '${ $.parentName }.street.0:value',
                observeMagentoStreet1:  '${ $.parentName }.street.1:value',
                observeMagentoStreet2:  '${ $.parentName }.street.2:value',
                observeMagentoStreet3:  '${ $.parentName }.street.3:value'
            },
        },


        getInternationalFormData: function () {
            var street;
            var postcode;
            var city;
            var country;

            var fields = [
                this.parentName + '.postcode',
                this.parentName + '.city',
                this.parentName + '.country_id'
            ];

            Registry.get(fields, function (postcodeElement, cityElement, countryElement) {
                postcode = postcodeElement.value();
                city = cityElement.value();
                country = countryElement.value();
            });

            var streetFieldBase = this.parentName + '.street.';
            street = '';
            for (var i = 0; i < 4; i++) {
                Registry.get(streetFieldBase + i.toString(), function (streetElement) {
                    if (i !== 0) {
                        street += ', ';
                    }
                    street += streetElement.value()
                });
            }
            street = street.trim();

            if (!street || !postcode || !city || !country) {
                return false;
            }

            return [street, postcode, city, country];
        },

        checkInternationalAddress: function () {
            var self = this;
            var formData = self.getInternationalFormData();

            self.isLoading(false);

            if (!window.checkoutConfig.shipping.postnl.is_international_address_active) {
                return;
            }

            self.addresses([]);
            self.handleError('');
            if (formData === false) {
                return;
            }

            self.isLoading(true);

            if (self.request !== undefined) {
                self.request.abort('avoidMulticall');
            }

            self.request = $.ajax({
                method: 'GET',
                url: window.checkoutConfig.shipping.postnl.urls.international_address,
                data: {
                    street: formData[0],
                    postcode: formData[1],
                    city: formData[2],
                    country: formData[3]
                },
            }).done(function (data) {
                self.handleInternationalResponse(data);
            }).fail(function (data) {
                if (data.statusText !== 'avoidMulticall') {
                    var errorMessage = $.mage.__('Unexpected error occurred. Please fill in the address details manually.');
                    self.handleError(errorMessage);
                }
            }).always(function (data) {
                self.isLoading(false);
            });
        },

        handleInternationalResponse: function (data) {
            var self = this;
            var message = data.message;

            this.isLoading(false);
            self.handleError(message);
            if ('addressMatchesFirst' in data && data.addressMatchesFirst === true) {
                this.messageClasses({'tig-postnl-success': true});
                this.addresses([]);
                return;
            }

            this.messageClasses({'tig-postnl-success': false});
            this.addresses(data.addresses);
        },

        updateFieldData() {
            if (this.timeout() !== null) {
                window.clearTimeout(this.timeout());
            }
            this.timeout(window.setTimeout(this.processUpdatedAddress.bind(this), this.timeoutDelay));
        },

        processUpdatedAddress: function() {
            this.timeout(null);
            var country;

            // Only apply the postcode check for NL
            Registry.get([this.parentName + '.country_id'], function (countryElement) {
                country = countryElement.value();
            });

            if (country !== 'NL') {
                this.checkInternationalAddress();
                return;
            }
            // Clear for NL
            this.isLoading(false);
            this.addresses([]);
            this.handleError('');
        },

        setAddress: function (data) {
            this.addresses([]);
            this.handleError('');

            var fields = [
                this.parentName + '.street',
                this.parentName + '.postcode',
                this.parentName + '.city',
                this.parentName + '.country_id'
            ];

            Registry.get(fields, function (streetFields, postcodeElement, cityElement, countryElement) {
                postcodeElement.value(data.postalCode);
                cityElement.value(data.cityName);
                countryElement.value(data.countryIso2);

                const streetLines = streetFields.elems();
                var strippedAddress = data.strippedAddress;
                // Make sure the address fits the number of fields
                while (strippedAddress.length > streetLines.length) {
                    const input = strippedAddress.pop();
                    strippedAddress[strippedAddress.length - 1] += ', ' + input;
                }

                for (const element of streetLines) {
                    element.value(strippedAddress.length === 0 ? '' : strippedAddress.shift());
                }
            });
        },

        handleError: function (errorMessage) {
            this.message(errorMessage);
        },

        observeHousenumber: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observePostcode: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeMagentoPostcode: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeMagentoCity: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeCountry: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeMagentoStreet0: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeMagentoStreet1: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeMagentoStreet2: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeMagentoStreet3: function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

    });
});
