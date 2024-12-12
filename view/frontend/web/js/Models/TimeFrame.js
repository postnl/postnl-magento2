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

            case 'noon':
                this.optionLabel = $.mage.__('Guaranteed');
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

            if (option == 'noon') {
                return window.checkoutConfig.shipping.postnl.noon_delivery_fee;
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
