/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define(['jquery'], function ($) {
    return function (data) {
        $.each(data, function (key, value) {
            this[key] = value;
        }.bind(this));

        this.optionLabel = '';

        /**
         * Make sure the label is translated, and the translation is available.
         */
        var option = this.option.toLowerCase();
        if (option == 'daytime') {
            this.optionLabel = $.mage.__('Daytime');
        } else if (option == 'evening') {
            this.optionLabel = $.mage.__('Evening');
        } else if (option == 'sunday') {
            this.optionLabel = $.mage.__('Sunday');
        }

        /**
         * Check if there is a fee available.
         */
        this.hasFee = function () {
            return this.getFee() !== '';
        };

        /**
         * Calculate the fee for this option
         */
        this.getFee = function () {
            var option = this.option.toLowerCase();

            if (option == 'evening') {
                return window.checkoutConfig.shipping.postnl.eveningdelivery_fee;
            }

            if (option == 'sunday') {
                return window.checkoutConfig.shipping.postnl.sundaydelivery_fee;
            }
        };
    };
});
