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
// @codingStandardsIgnoreFile
define([
    'jquery',
    'Magento_Ui/js/lib/view/utils/async',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/view/shipping',
    'TIG_PostNL/js/Helper/State',
    'Magento_Checkout/js/model/quote',
    'uiRegistry'
], function (
    $,
    async,
    setShippingInformationAction,
    Shipping,
    State,
    quote,
    registry
) {
    'use strict';

    var instance = null;

    // Fix js error in Magento 2.2
    function fixAddress(address) {
        if (!address) {
            return;
        }

        if (Array.isArray(address.street) && address.street.length == 0) {
            address.street = ['', ''];
        }
    }

    function removeAmazonPayButton() {
        var amazonPaymentButton = $('#PayWithAmazon_amazon-pay-button img');
        if (amazonPaymentButton.length > 1) {
            amazonPaymentButton.not(':first').remove();
        }
    }

    return Shipping.extend({
        setShippingInformation: function () {
            fixAddress(quote.shippingAddress());
            fixAddress(quote.billingAddress());

            setShippingInformationAction().done(
                function () {
                    //stepNavigator.next();
                }
            );
        },
        initialize: function () {
            this._super();
            instance = this;

            registry.get('checkout.steps.shipping-step.shippingAddress.before-form.amazon-widget-address.before-widget-address.amazon-checkout-revert',
                function (component) {
                    component.isAmazonAccountLoggedIn.subscribe(function (loggedIn) {
                        if (!loggedIn) {
                            registry.get('checkout.steps.shipping-step.shippingAddress', function (component) {
                                if (component.isSelected()) {
                                    component.selectShippingMethod(quote.shippingMethod());
                                }
                            });
                        }
                    });
                }
            );

            registry.get('checkout.steps.billing-step.payment.payments-list.amazon_payment', function (component) {
                if (component.isAmazonAccountLoggedIn()) {
                    $('button.action-show-popup').hide();
                }
            });

            registry.get('checkout.steps.shipping-step.shippingAddress.customer-email.amazon-button-region.amazon-button',
                function (component) {
                    async.async({
                        selector: "#PayWithAmazon_amazon-pay-button img"
                    }, function () {
                        removeAmazonPayButton();
                    });

                    component.isAmazonAccountLoggedIn.subscribe(function (loggedIn) {
                        if (!loggedIn) {
                            removeAmazonPayButton();
                        }
                    });
                }
            );
        },

        selectShippingMethod: function (shippingMethod) {
            this._super();

            instance.setShippingInformation();

            return true;
        },

        canUseDeliveryOption: function () {
            var deliveryOptionsActive = window.checkoutConfig.shipping.postnl.shippingoptions_active == 1;
            var deliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
            var pakjegemakActive = window.checkoutConfig.shipping.postnl.pakjegemak_active == '1';

            return deliveryOptionsActive && (deliveryDaysActive || pakjegemakActive);
        },

        isPostNLDeliveryMethod: function (method) {
            return method.carrier_code == 'tig_postnl';
        },

        canUsePostnlDeliveryOptions: function (method) {
            if (!this.canUseDeliveryOption()) {
                return false;
            }

            var result = this.isPostNLDeliveryMethod(method);

            if (result) {
                State.method(method);
            }

            return result;
        }
    });
});
