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
    'Magento_Checkout/js/view/shipping-information/address-renderer/default',
    'ko',
    'jquery',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'TIG_PostNL/js/Helper/State'
], function (
    Component,
    ko,
    $,
    stepNavigator,
    sidebarModel,
    State
) {
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/Pakjegemak/Address',
            isVisible: false,
            address: {
                company: '',
                prefix: '',
                firstname: '',
                lastname: '',
                suffix: '',
                street: '',
                city: '',
                region: '',
                postcode: '',
                countryId: '',
                telephone: ''
            }
        },

        initObservable : function () {
            this._super().observe([
                'address',
                'isVisible'
            ]);

            this.address = State.pickupAddress;
            this.isVisible = ko.computed(function () {
                return State.currentSelectedShipmentType() === 'pickup';
            });

            return this;
        },

        back: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping');
        }
    });
});
