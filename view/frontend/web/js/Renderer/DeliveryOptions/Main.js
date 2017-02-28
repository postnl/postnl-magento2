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
    'uiComponent',
    'ko',
    'TIG_PostNL/js/Helper/State',
    'TIG_PostNL/js/Helper/AddressFinder'
], function (
    Component,
    ko,
    State,
    AddressFinder
) {
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/DeliveryOptions/Main',
            shipmentType: 'delivery'
        },

        initObservable: function () {
            this._super().observe([
                'shipmentType'
            ]);

            this.isLoading = ko.computed(function () {
                return State.isLoading();
            });

            return this;
        },

        canUseDeliveryOptions: ko.computed(function () {
            return State.deliveryOptionsAreAvailable() && AddressFinder() !== false;
        }),

        canUsePickupLocations: ko.computed(function () {
            return window.checkoutConfig.shipping.postnl.pakjegemak_active == 1 && State.pickupOptionsAreAvailable();
        }),

        setDelivery: function () {
            this.shipmentType('delivery');
        },

        setPickup: function () {
            this.shipmentType('pickup');
        }
    });
});
