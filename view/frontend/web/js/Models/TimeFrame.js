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
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function (
    $,
    quote,
    priceUtils
) {
    return function (data) {
        $.each(data, function (key, value) {
            this[key] = value;
        }.bind(this));


        /**
         * Make sure the label is translated, and the translation is available.
         */
        this.optionLabel = '';
        var option = this.option.toLowerCase();
        switch (option) {
            case 'daytime':
                this.optionLabel = $.mage.__('Daytime');
                break;

            case 'evening':
                this.optionLabel = $.mage.__('Evening');
                break;

            case 'sunday':
                this.optionLabel = $.mage.__('Sunday');
                break;

            case 'today':
                this.optionLabel = $.mage.__('Fast Delivery');
                break;
        }

        /**
         * Check if there is a fee available.
         */
        this.hasFee = function () {
            var fee = this.getFee();
            return fee !== 0 && fee !== '';
        };

        /**
         * Calculate the fee for this option
         */
        this.getFee = function () {
            var option = this.option.toLowerCase();

            if (option == 'evening') {
                return this.getEveningFee();
            }

            if (option == 'sunday') {
                return window.checkoutConfig.shipping.postnl.sundaydelivery_fee;
            }

            if (option == 'today') {
                return window.checkoutConfig.shipping.postnl.todaydelivery_fee;
            }

            return 0;
        };

        /**
         * Calculate the evening fee for the country
         */
        this.getEveningFee = function () {
            if (this.address.country === 'BE') {
                return window.checkoutConfig.shipping.postnl.eveningdelivery_be_fee;
            }

            return window.checkoutConfig.shipping.postnl.eveningdelivery_fee;
        };

        /**
         * Format the fee
         */
        this.getFeeFormatted = function () {
            return priceUtils.formatPrice(this.getFee(), quote.getPriceFormat());
        };
    };
});
