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
            postcode    : null,
            country     : null,
            street      : null,
            firstname   : null,
            lastname    : null,
            telephone   : null,
            housenumber : null
        },
        countryCode,
        timer,
        allFieldsExists = true,
        valueUpdateNotifier = ko.observable(null);

    var notifySubscribers = function () {
        if (typeof timer !== 'undefined') {
            clearTimeout(timer);
        }

        timer = setTimeout(function () {
            valueUpdateNotifier.notifySubscribers();
        });
    };

    var RegistryFields = [
        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0',
        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id'
    ];

    if (window.checkoutConfig.shipping.postnl.is_postcodecheck_active) {
        RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.postcode');
        RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber');
    } else {
        RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode');
        RegistryFields.push('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.1');
    }

    uiRegistry.get(RegistryFields, function (streetFirstLine, countryField, postcodeField, houseNumberField) {
        // Notify first time in case address information is stored in quote
        if (countryCode === undefined) {
            countryCode = countryField.value();
        }

        setTimeout(function() {
            notifySubscribers();
        }, 2000);
        streetFirstLine.value.subscribe(function () {
            notifySubscribers();
        });

        countryField.value.subscribe(function (val) {
            countryCode = val;
            notifySubscribers();
        });

        postcodeField.value.subscribe(function () {
            notifySubscribers();
        });

        houseNumberField.value.subscribe(function () {
            notifySubscribers();
        });
    });

    var fields = [
        "input[name*='street[0]']",
        "input[name*='street[1]']",
        "input[name*='postcode']",
        "select[name*='country_id']"
    ];

    /**
     * Collect the needed information from the quote
     */
    return ko.computed(function () {
        valueUpdateNotifier();

        var housenumber;
        if (window.checkoutConfig.postcode !== undefined ||
            window.checkoutConfig.shipping.postnl.is_postcodecheck_active) {
            housenumber = $("input[name*='tig_housenumber']").val();
        }

        /**
         * The street is not always available on the first run.
         */
        var shippingAddress = quote.shippingAddress();
        var quoteStreet = [];
        if (shippingAddress) {
            quoteStreet = (typeof shippingAddress.street === 'undefined') ? [] : shippingAddress.street;
        }

        if (customer.isLoggedIn() && shippingAddress && quoteStreet.length > 0) {
            address = {
                street: quoteStreet,
                postcode: shippingAddress.postcode,
                lastname: shippingAddress.lastname,
                firstname: shippingAddress.firstname,
                telephone: shippingAddress.telephone,
                country: shippingAddress.countryId,
                housenumber : housenumber
            };

            return address;
        }

        allFieldsExists = true;
        $.each(fields, function () {
            /** Second street may not exist and is therefor not required and should only be observed. */
            if (!$(this).length && this !== "input[name*='street[1]']") {
                allFieldsExists = false;
                return false;
            }
        });

        if (!allFieldsExists) {
            return null;
        }

        /**
         * Unfortunately Magento does not always fill all fields, so get them ourselves.
         */
        address.street = {
            0 : $("input[name*='street[0]']").val(),
            1 : $("input[name*='street[1]']").val()
        };

        if (housenumber !== undefined) {
            address.housenumber = housenumber;
        }

        address.postcode   = $("input[name*='postcode']").val();
        address.firstname  = $("input[name*='firstname']").val();
        address.lastname   = $("input[name*='lastname']").val();
        address.telephone  = $("input[name*='telephone']").val();

        if (!address.country || address.country !== countryCode) {
            address.country = $("select[name*='country_id']").val();
        }

        if (!address.country || !address.postcode || !address.street[0]) {
            return false;
        }

        return address;


    }.bind(this));
});
