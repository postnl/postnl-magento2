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
define([
    'ko',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/shipping-service'
], function (
    ko,
    selectShippingMethodAction,
    checkoutData,
    shippingService
) {
    var deliveryOptionsAreLoading = ko.observable(false);
    var pickupOptionsAreLoading = ko.observable(false);
    var fee = ko.observable(null);

    var isLoading = ko.computed(function () {
        return deliveryOptionsAreLoading() || pickupOptionsAreLoading();
    });

    fee.subscribe(function (value) {
        var shippingRates = shippingService.getShippingRates()();

        if (value == 0) {
            value = null;
        }

        /**
         * For some unknown reason the rates would not update on the easy way (rate['fee'] = fee).
         * That's why we had to follow this path.
         */
        ko.utils.arrayForEach(shippingRates, function (rate, index) {
            if (rate.carrier_code != 'tig_postnl') {
                return;
            }

            delete rate.fee;

            var newRate = {
                'fee': value
            };

            ko.utils.arrayForEach(Object.keys(rate), function (key) {
                newRate[key] = rate[key];
            });

            shippingRates[index] = newRate;
        });

        shippingService.setShippingRates(shippingRates);
    });

    return {
        deliveryOptionsAreLoading: deliveryOptionsAreLoading,
        pickupOptionsAreLoading: pickupOptionsAreLoading,
        isLoading: isLoading,
        method: ko.observable(null),
        fee: ko.observable(null),

        selectShippingMethod: function () {
            selectShippingMethodAction(this.method());
            checkoutData.setSelectedShippingRate(this.method().carrier_code + '_' + this.method().method_code);

            return true;
        }
    };
});
