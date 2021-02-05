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
    'Magento_Checkout/js/model/quote',
    'jquery',
    'Magento_Customer/js/model/customer',
    'uiRegistry'
], function (
    ko,
    quote,
    $,
    customer,
    uiRegistry
) {
    'use strict';

    var address = {
        country     : null,
        street      : null,
        postcode    : null,
        housenumber : null,
        firstname   : null,
        lastname    : null
    };

    /**
     * Collect the needed information from the quote
     */
    return ko.computed(function () {
        /**
         * Force AddressFinder subscribers to run - the ko.computed function will read the
         * quote.shippingAddress() value. When the quote.shippingAddress() changes, our code willrun
         */
        var shippingAddress = quote.shippingAddress();

        // Country is required to determine which fields are used.
        uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id', function (countryField) {
            address.country = countryField.value();
        });

        var RegistryFields = [
            'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.firstname',
            'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.lastname',
            'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0'
        ];

        if (address.country === 'NL' && (window.checkoutConfig.shipping.postnl.is_postcodecheck_active || window.checkoutConfig.postcode.postcode_active)) {
            RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.postcode');
            RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber');
        } else {
            RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode');
            RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.1');
        }

        uiRegistry.get(RegistryFields, function (firstnameField, lastnameField, streetFirstLine, postcodeField, houseNumberField) {
            // The housenumber value could be empty, and could have been included in the first address line.
            var housenumber = houseNumberField.value();
            if (!housenumber) {
                housenumber = streetFirstLine.value().replace(/\D/g,'');
            }

            address.street = [streetFirstLine.value()];
            address.postcode = postcodeField.value();
            address.housenumber = housenumber;
            address.firstname = firstnameField.value();
            address.lastname = lastnameField.value();
        });

        // Some merchants disable the telephone field. Adding this to the previous part will stop the entire get function
        uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.telephone', function (telephoneField) {
            address.telephone = telephoneField.value();
        });

        if (!address.country || !address.postcode || !address.street[0] || !address.housenumber) {
            return false;
        }

        var nlRegex = new RegExp('^[1-9][0-9]{3}\s?[a-zA-Z]{2}$');
        // No regex required for BE, valid BE zipcodes are between 1000 and 9999.
        if ((address.country === 'NL' && !nlRegex.test(address.postcode)) ||
            (address.country === 'BE' && !(address.postcode >= 1000 && address.postcode <= 9999))) {
            return false;
        }

        return address;
    }.bind(this));
});
