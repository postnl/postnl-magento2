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
    'Magento_Checkout/js/model/quote'
], function (
    $,
    quote
) {
    'use strict';

    return function (Component) {
        return Component.extend({
            tigHouseNumber: function() {
                if (quote.billingAddress().customAttributes.length > 0) {
                    var houseNumberString = "";
                    var customAttributes = quote.billingAddress().customAttributes;

                    // check if custom attribute exists and is not empty
                    customAttributes.forEach(function (attribute) {
                        if (attribute.attribute_code === "tig_housenumber" && attribute.value !== "") {
                            houseNumberString = attribute.value;
                        }

                        if (attribute.attribute_code === "tig_housenumber_addition" && attribute.value !== "") {
                            houseNumberString += " " + attribute.value;
                        }
                    });

                    return houseNumberString;
                }
            }
        });
    };
});
