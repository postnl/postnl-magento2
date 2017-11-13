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
    'Magento_Customer/js/model/customer'
], function (
    ko,
    quote,
    $,
    customer
) {
    'use strict';

    var address = {
            postalCode  : null,
            countryCode : null,
            street      : null,
            firstname   : null,
            lastname    : null,
            telephone   : null
        },
        countryCode,
        timer,
        allFieldsExists = true,
        valueUpdateNotifier = ko.observable(null);

    var fields = [
        "input[name*='street[0]']",
        "input[name*='street[1]']",
        "input[name*='postcode']",
        "select[name*='country_id']"
    ];

    /**
     * Without cookie data Magento is not observing the fields so the AddressFinder is never triggered.
     * The Timeout is needed so it gives the Notifier the chance to retrieve the correct country code,
     * and not the default value.
     */
    $(document).on('change', fields.join(','), function () {
        // Clear timeout if exists.
        if (typeof timer !== 'undefined') {
            clearTimeout(timer);
        }

        timer = setTimeout(function () {
            countryCode = $("select[name*='country_id']").val();
            valueUpdateNotifier.notifySubscribers();
        }, 500);
    });

    /**
     * Collect the needed information from the quote
     */
    return ko.computed(function () {
        valueUpdateNotifier();

        /**
         * The street is not always available on the first run.
         */
        var shippingAddress = quote.shippingAddress();
        if (customer.isLoggedIn() && shippingAddress && shippingAddress.street) {
            address = {
                street: shippingAddress.street,
                postalCode: shippingAddress.postcode,
                lastname: shippingAddress.lastname,
                firstname: shippingAddress.firstname,
                telephone: shippingAddress.telephone,
                countryCode: shippingAddress.countryId
            };

            return address;
        }

        allFieldsExists = true;
        $.each(fields, function () {
            if (!$(this).length) {
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

        address.postalCode = $("input[name*='postcode']").val();
        address.firstname  = $("input[name*='firstname']").val();
        address.lastname   = $("input[name*='lastname']").val();
        address.telephone  = $("input[name*='telephone']").val();

        if (!address.countryCode || address.countryCode !== countryCode) {
            address.countryCode = $("select[name*='country_id']").val();
        }

        if (!address.countryCode || !address.postalCode || !address.street) {
            return false;
        }

        return address;
    }.bind(this));
});
